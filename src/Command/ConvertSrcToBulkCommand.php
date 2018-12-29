<?php

namespace App\Command;

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

    protected static $defaultName = "dso:convert-bulk";

    protected static $listType = ['dso20', 'constellations', 'constellations'];

    const PATH_SOURCE = '/config/elasticsearch/sources/';
    const BULK_SOURCE = '/config/elasticsearch/bulk/';


    /**
     * ConvertSrcToBulkCommand constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel->getProjectDir();
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

            if (file_exists($inputFile) && is_readable($inputFile)) {
                $data = $this->openFile($inputFile);
                if (JSON_ERROR_NONE === json_last_error()) {
                    $handle = fopen($outputFilename, 'w');

                    foreach ($data as $key=>$inputData) {
                        $id = (array_key_exists('id', $inputData)) ? $inputData['id']: null;
                        $line = json_encode($inputData);

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

                        fwrite($handle, $lineReplace . PHP_EOL);
                        if (1 == $key) {
                            die();
                        }
                    }
                    fclose($handle);
                } else {
                    $output->writeln(sprintf("Error JSON : %s", json_last_error_msg()));
                }
            } else {
                $output->writeln(sprintf("File %s not exist or not readable", $inputFile));
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
     * @return string
     */
    public static function md5ForId($id): string {
        return md5(uniqid($id) . microtime());
    }

    /**
     * @param $id
     * @return string
     */
    public static function getCatalog($id): string
    {
        return Utils::getCatalogMapping()[substr($id, 0, 2)];
    }

    /**
     * @return null
     */
    public static function getItemOrder()
    {
        return null;
    }
}