<?php


namespace App\Command;


use App\Repository\DsoRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckAstrobinImageCommand
 * @package App\Command
 */
class CheckAstrobinImageCommand extends Command
{

    protected static $defaultName = "dso:check-astrobin";

    /** @var DsoRepository */
    protected $dsoRepository;

    /**
     * CheckAstrobinImageCommand constructor.
     *
     * @param DsoRepository $dsoRepository
     */
    public function __construct(DsoRepository $dsoRepository)
    {
        $this->dsoRepository = $dsoRepository;

        parent::_construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setDescription('return Astrobin ImageId failed');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $listAstrobinId = $this->dsoRepository->getAstrobinId();
        dump($listAstrobinId);
    }


}
