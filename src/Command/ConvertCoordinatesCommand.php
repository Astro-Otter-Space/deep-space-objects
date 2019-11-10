<?php


namespace App\Command;

use App\Classes\Utils;
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

    const REGEX = '/[-−0-9]+([,.][0-9]+)?/';

    protected static $defaultName = 'dso:convert-coordinates';

    /**
     *
     */
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lon = $lat = 0;
        if (true === $input->hasOption('ra')) {
            $raOption = $input->getOption('ra') ?? null;
            $lon = Utils::raToLon($raOption);
        }

        if (true === $input->hasOption('dec')) {
            $decOption = $input->getOption('dec') ?? null;
            $lat = Utils::decToLat($decOption);
        }

        $output->writeln([$lon, $lat]);
    }

}
