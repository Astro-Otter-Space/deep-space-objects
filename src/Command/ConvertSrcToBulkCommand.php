<?php

namespace App\Command;

use App\Classes\CacheInterface;
use App\Classes\Utils;
use App\Entity\Dso;
use App\Entity\ListDso;
use App\Entity\UpdateData;
use App\Repository\DsoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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

    const PATH_SOURCE = '/config/elasticsearch/sources/';
    const BULK_SOURCE = '/config/elasticsearch/bulk/';

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
            ->addOption('import', InputArgument::REQUIRED, 'List of import : ' . implode(', ', self::$listTypeImport))
            ->addArgument('type', InputArgument::REQUIRED, 'List of values : ' . implode(', ', self::$listIndexType));
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
        $updateData = $this->em->getRepository(UpdateData::class)->findOneBy(['id' => 'DESC']);

        /** @var \DateTimeInterface $lastUpdateDate */
        $lastUpdateDate = $updateData->getDate() ?? new \DateTime('now');

        if ($input->hasArgument('type') && in_array($input->getArgument('type'), self::$listIndexType)) {

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
                        $line = json_encode(Utils::utf8_encode_deep($inputData), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        $mapping = [
                            'randId' => 'md5ForId',
                            'catalog' => 'getCatalog',
                            'order' => 'getItemOrder'
                        ];
                        $lineReplace = preg_replace_callback('#%(.*?)%#', function($match) use ($mapping, $id) {
                            $findKey = $match[1];
                            if (in_array($findKey, array_keys($mapping))) {
                                $method = $mapping[$findKey];
                                return self::$method($id);
                            } else {
                                return "%s".$findKey."%s";
                            }
                        }, $line);

                        // If full :
                        fwrite($handle, $this->buildCreateLine($type, $id) . PHP_EOL);
                        fwrite($handle, utf8_decode($lineReplace) . PHP_EOL);

                        // If delta
                    }


                    /**
                     * STEP 2 : delete index and rebuild mapping ?
                     */

                    /**
                     * STEP 3 : import data
                     */

                    /**
                     * Step 4 : get list of updated data
                     */
                    $listDsoId = [];
                    // TODO : retrieve list of dso where date update are between lastUpdateDate and Now

                    /** @var ListDso $listDso */
                    $listDso = $this->dsoRepository->getObjectsUpdatedAfter($lastUpdateDate);
                    if (0 < $listDso->getIterator()->count()) {
                        while($listDso->getIterator()->valid()) {

                            /** @var Dso $dso */
                            $dso = $listDso->getIterator()->current();

                            array_push($listDsoId, $dso->getId());

                            // TODO : add locale
                            $listIdMd5 = array_map(function($locale) use ($dso){
                                return sprintf('%s_%s', self::md5ForId($dso->getId()), $locale);
                            }, $this->listLocales);

                            foreach ($listIdMd5 as $idMd5) {
                                if ($this->cacheUtil->hasItem($idMd5)) {
                                    $this->cacheUtil->deleteItem($idMd5);
                                }
                            }

                        }
                    }

                    /** @var UpdateData $newLastUpdate */
                    $newLastUpdate = new UpdateData();
                    $newLastUpdate->setDate(new \DateTime('now'));
                    $newLastUpdate->setListDso($listDsoId);

                    $this->em->persist($newLastUpdate);
                    $this->em->flush();

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
        // {"create": {"_index": "<type>", "_type": "_doc", "_id": "<md5 id>"}},
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
