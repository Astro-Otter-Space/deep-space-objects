<?php

namespace App\Command;

use App\Classes\CacheInterface;
use App\Classes\Utils;
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
    protected static $defaultName = "dso:convert-bulk";

    protected static $listType = ['dso20', 'constellations'];

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
     */
    public function __construct(KernelInterface $kernel, CacheInterface $cacheUtil)
    {
        $this->kernel = $kernel->getProjectDir();
        $this->cacheUtil = $cacheUtil;
        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setDescription('Convert source file into bulk for Elastic Search')
            ->addArgument('type', InputArgument::REQUIRED, 'List of values : ' . implode(', ', self::$listType));
        ;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasArgument('type') && in_array($input->getArgument('type'), self::$listType)) {

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

                        // After import, we delete cache
                        $idMd5 = self::md5ForId($id);
                        if ($this->cacheUtil->hasItem($idMd5)) {
                            $this->cacheUtil->deleteItem($idMd5);
                        }

                        fwrite($handle, $this->buildCreateLine($type, $id) . PHP_EOL);
                        fwrite($handle, utf8_decode($lineReplace) . PHP_EOL);
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
        // {"create": {"_index": "<type>", "_type": "_doc", "_id": "<md5 id>"}},
        return  sprintf('{"create": {"_index": "%s", "_type": "_doc", "_id": "%s"}}',self::$mapping[$type], self::md5ForId($id));
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
            return Utils::getCatalogMapping()[substr($id, 0, 2)];
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
