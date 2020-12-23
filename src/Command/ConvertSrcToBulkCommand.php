<?php

namespace App\Command;

use App\Classes\CacheInterface;
use App\Classes\Utils;
use App\Entity\BDD\UpdateData;
use App\Entity\ES\Dso;
use App\Entity\ES\ListDso;
use App\Repository\DsoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ConvertSrcToBulkCommand
 * @package App\Command
 */
class ConvertSrcToBulkCommand extends Command
{
    /** @var KernelInterface */
    private $kernel;

    /** @var CacheInterface */
    private $cacheUtil;

    /** @var EntityManagerInterface */
    private $em;

    /** @var DsoRepository */
    private $dsoRepository;

    /** @var array  */
    protected $listLocales;

    protected static $defaultName = "dso:convert-bulk";

    protected static $listTypeImport = ['full', 'delta'];
    protected static $listIndexType = ['dso20', 'constellations'];

    protected static $mapping = [
        'dso20' => 'deepspaceobjects',
        'constellations' => 'constellations'
    ];

    public const PATH_SOURCE = '/config/elasticsearch/sources/';
    public const BULK_SOURCE = '/config/elasticsearch/bulk/';

    /**
     * ConvertSrcToBulkCommand constructor.
     *
     * @param KernelInterface $kernel
     * @param CacheInterface $cacheUtil
     * @param EntityManagerInterface $em
     * @param DsoRepository $dsoRepository
     * @param $listLocales
     */
    public function __construct(KernelInterface $kernel, CacheInterface $cacheUtil, EntityManagerInterface $em, DsoRepository $dsoRepository, $listLocales)
    {
        $this->kernel = $kernel->getProjectDir();
        $this->cacheUtil = $cacheUtil;
        $this->em = $em;
        $this->dsoRepository = $dsoRepository;
        $this->listLocales = explode('|', $listLocales);
        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setDescription('Convert source file into bulk for Elastic Search')
            ->addOption('import','i',  InputOption::VALUE_REQUIRED, 'List of import : ' . implode(', ', self::$listTypeImport))
            ->addArgument('type', null,InputArgument::REQUIRED, 'List of values : ' . implode(', ', self::$listIndexType));
        ;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UpdateData $updateData */
        $lastImport = $this->em->getRepository(UpdateData::class)->findOneBy([], ['date' => 'DESC']);

        /** @var \DateTimeInterface $lastUpdateDate */
        $lastImportDate = $lastImport->getDate() ?? new \DateTime('now');
        $lastImportDate->setTimezone(new \DateTimeZone('Europe/Paris'));


        $output->writeln(sprintf("Last update : %s", $lastImportDate->format(Utils::FORMAT_DATE_ES)));
        $typeImport = $input->hasOption('import') ? $input->getOption('import') : 'delta';

        if ($input->hasArgument('type') && in_array($input->getArgument('type'), self::$listIndexType, true)) {

            $type = $input->getArgument('type');

            $inputFilename = sprintf('%s.src.json', $type);
            $inputFile = $this->kernel . self::PATH_SOURCE . $inputFilename;

            $outputFilename = $this->kernel . self::BULK_SOURCE . sprintf('%s.bulk.json', $type);

            $outputDirName = dirname($outputFilename);

            if (!file_exists($outputDirName)) {
                mkdir(dirname($outputFilename), '0755');
            }

            if (!file_exists($outputFilename)) {
                $fp = fopen($outputFilename, 'w+');
                fclose($fp);
            }

            if (is_readable($inputFile)) {
                $data = $this->openFile($inputFile);
                $bulkData = [];
                if (JSON_ERROR_NONE === json_last_error()) {
                    $handle = fopen($outputFilename, 'w');

                    /**
                     * STEP 1 : build bulk
                     */
                    foreach ($data as $inputData) {
                        if (!array_key_exists('id', $inputData)) {
                            continue;
                        }

                        $id = $inputData['id'];

                        if (array_key_exists('updated_at', $inputData) && !is_null($inputData['updated_at'])) {
                            $mode = 'update';
                        } else {
                            $newUpdatedAt = new \DateTime();
                            $mode = 'create';
                            $inputData['updated_at'] = $newUpdatedAt->format(Utils::FORMAT_DATE_ES);
                        }

                        $line = json_encode(Utils::utf8_encode_deep($inputData), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        $mapping = [
                            'randId' => 'md5ForId',
                            'catalog' => 'getCatalog',
                            'order' => 'getItemOrder'
                        ];
                        $lineReplace = preg_replace_callback('#%(.*?)%#', static function($match) use ($mapping, $id) {
                            $findKey = $match[1];
                            if (array_key_exists($findKey, $mapping)) {
                                $method = $mapping[$findKey];
                                return self::$method($id);
                            } else {
                                return "%s".$findKey."%s";
                            }
                        }, $line);

                        /**
                         * CREATE
                         */
                        if ('create' === $mode) {
                            if ('delta' === $typeImport) {
                                $bulkData[] = [
                                    'idDoc' => self::md5ForId($id),
                                    'mode' => 'create',
                                    'data' => json_decode(utf8_decode($lineReplace), true, 512, JSON_THROW_ON_ERROR)
                                ];

                            } elseif ('full' === $typeImport) {
                                $bulkLine =$this->buildCreateLine($type, $id);
                                fwrite($handle, $bulkLine . PHP_EOL);
                                fwrite($handle, utf8_decode($lineReplace) . PHP_EOL);
                            }

                            $output->writeln(sprintf('[%s] item %s', $mode, $id));

                        /**
                         * UPDATE
                         */
                        } elseif ('update' === $mode) {
                            if ('delta' === $typeImport) {
                                // fow now, only delta
                                $lastUpdateData = \DateTime::createFromFormat(Utils::FORMAT_DATE_ES, $inputData['updated_at']);
                                if (0 === $lastImportDate->diff($lastUpdateData)->invert) {


                                    array_push($bulkData, [
                                        'idDoc' => self::md5ForId($id),
                                        'mode' => 'update',
                                        'data' => json_decode(utf8_decode($lineReplace), true)
                                    ]);
                                    $output->writeln(sprintf('[%s] item %s', $mode, $id));
                                }

                            } elseif ('full' === $typeImport) {
                                $bulkLine =$this->buildCreateLine($type, $id);
                                fwrite($handle, $bulkLine . PHP_EOL);
                                fwrite($handle, utf8_decode($lineReplace) . PHP_EOL);
                            }

                        }
                    }

                    if ('delta' === $input->getOption('import')) {
                        /**
                         * STEP 2 : import data
                         */
                        $bulk = $this->dsoRepository->bulkImport($bulkData);

                        if (true === $bulk) {
                            $output->writeln('Wait indexing new data...');
                            sleep(5);

                            /**
                             * Step 3 : get list of updated data
                             */
                            /** @var ListDso $listDso */
                            $listDso = $this->dsoRepository->getObjectsUpdatedAfter($lastImportDate);
                            if (0 < $listDso->getIterator()->count()) {
                                /**
                                 * STEP 4 : update DB
                                 * TODO : move to repository
                                 */
                                $listDsoAsArray = array_map(function (Dso $dso) {
                                    return $dso->getId();
                                }, iterator_to_array($listDso));

                                /** @var \DateTimeInterface $now */
                                $now = new \DateTime('now');
                                $now->setTimezone(new \DateTimeZone('Europe/Paris'));

                                $output->writeln(sprintf("Save in table Update_data, lastUpdate Bulk : %s", $now->format(Utils::FORMAT_DATE_ES)));
                                /** @var UpdateData $newLastUpdate */
                                $newLastUpdate = new UpdateData();
                                $newLastUpdate->setDate($now);
                                $newLastUpdate->setListDso($listDsoAsArray);

                                $this->em->persist($newLastUpdate);
                                $this->em->flush();

                                /**
                                 * STEP 5 empty cache
                                 */
                                /** @var Dso $dsoCurrent */
                                foreach (iterator_to_array($listDso) as $dsoCurrent) {

                                    $id = strtolower($dsoCurrent->getId());
                                    if (!empty($dsoCurrent->getAlt())) {
                                        $name = Utils::camelCaseUrlTransform($dsoCurrent->getAlt());
                                        $id = implode(trim($dsoCurrent::URL_CONCAT_GLUE), [$id, $name]);
                                    }

                                    $listMd5Dso = array_merge(array_map(static function ($locale) use ($id) {
                                        return md5(sprintf('%s_%s', $id, $locale));
                                    }, $this->listLocales), [md5(sprintf('%s_cover', $id))]);

                                    array_walk($listMd5Dso, function ($idMd5) use ($dsoCurrent, $output) {
                                        if ($this->cacheUtil->hasItem($idMd5)) {
                                            $output->writeln(sprintf("[Cache pool %s] Empty cache %s", $dsoCurrent->getId(), $idMd5));
                                            $this->cacheUtil->deleteItem($idMd5);
                                        }
                                    });
                                }
                            }
                        } else {
                            $output->writeln("No bulk import");
                        }
                    }

                    fclose($handle);
                } else {
                    $output->writeln(sprintf("Error JSON : %s", json_last_error_msg()));
                }
            } else {
                $output->writeln(sprintf("File %s not readable", $inputFile));
            }
        } else {
            $output->writeln(sprintf("Argument %s not available", $input->getArgument('type')));
        }
    }

    /**
     * Open file and convert into array
     * @param $file
     * @return mixed
     */
    private function openFile($file): array {
        return json_decode(file_get_contents($file), true);
    }


    /**
     * @param $type
     * @param $id
     *
     * @return string
     */
    public function buildCreateLine($type, $id): string
    {
        return  sprintf('{"create": {"_index": "%s", "_type": "_doc", "_id": "%s"}}', self::$mapping[$type], self::md5ForId($id));
    }

    /**
     * @param $type
     * @param $id
     *
     * @return string
     */
    public function buildUpdateLine($type, $id): string
    {
        return sprintf('{"update": {"_id": "%s", "_index": "%s"}}', self::md5ForId($id), self::$mapping[$type]);
    }

    /**
     * @return string
     */
    public static function md5ForId($id): string {
        return md5($id);
    }

    /**
     * @param $id
     * @return string
     */
    public static function getCatalog($id): string
    {
        if (!is_null($id)) {
            return Utils::getCatalogMapping()[substr($id, 0, 2)] ?? Utils::UNASSIGNED;
        }
        return Utils::UNASSIGNED;
    }

    /**
     * @return null
     */
    public static function getItemOrder()
    {
        return null;
    }

}
