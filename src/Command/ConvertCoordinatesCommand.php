<?php

//declare(strict_types=1);

namespace App\Command;

use App\Classes\Utils;
use App\Service\ConvertCoordinates;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConvertCoordinatesCommand
 * @package App\Command
 */
class ConvertCoordinatesCommand extends Command
{
    protected static $defaultName = 'dso:convert-coordinates';
    public const REGEX = '/[-−0-9]+([,.][0-9]+)?/';

    protected function configure()
    {
        $this
            ->setDescription('Convert Ra/Dec coordinates into DMS coordinates')
            ->addOption('ra', null,InputOption::VALUE_OPTIONAL, 'ra coordinate')
            ->addOption('dec',  null,InputOption::VALUE_OPTIONAL, 'dec coordinates');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $lon = $lat = 0;
        $convertCoordinates = new ConvertCoordinates;
        if (true === $input->hasOption('ra')) {
            $value = $input->getOption('ra') ?: 0;
            $lon = $convertCoordinates('raToLon', $value);
        }

        if (true === $input->hasOption('dec')) {
            $value = $input->getOption('dec') ?: 0;
            $lat = $convertCoordinates('decToLat', $value);
        }

        $output->writeln([$lon, $lat]);
        return Command::SUCCESS;
    }

}
