<?php


namespace App\Command;

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

            preg_match_all(self::REGEX, $raOption, $matches, PREG_PATTERN_ORDER);
            if (!is_null($raOption)) {
                $h = (int)$matches[0][0];
                $mn = (int)$matches[0][1];
                $sec = (float)$matches[0][2];

                $lon = ($h + ($mn/60) + ($sec/3600))*15;
                $lon = ($lon > 180) ? $lon-360 : $lon;
            }

        }

        if (true === $input->hasOption('dec')) {
            $decOption = $input->getOption('dec') ?? null;
            if (!is_null($decOption)) {
                preg_match_all(self::REGEX, $decOption, $matches, PREG_PATTERN_ORDER);

                $deg = (int)str_replace('−', '-', $matches[0][0]);
                $mn = (int)$matches[0][1];
                $sec = (float)$matches[0][2];

                $lat = $deg + $mn/60 + $sec[3][0]/3600;
            }
        }

        $output->writeln([$lon, $lat]);
    }

}
