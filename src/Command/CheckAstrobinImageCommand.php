<?php


namespace App\Command;


use App\Repository\DsoRepository;
use Astrobin\Exceptions\WsResponseException;
use Astrobin\Response\Image;
use Astrobin\Services\GetImage;
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
        parent::__construct();
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
     * @throws \Astrobin\Exceptions\WsException
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        dump(__CLASS__);
        $failedAstrobinId = [];
        $listAstrobinId = $this->dsoRepository->getAstrobinId();
        if (0 < count($listAstrobinId)) {
            foreach ($listAstrobinId as $astrobinId) {
                try {
                    /** @var GetImage $image */
                    $image = new GetImage();

                    $result = $image->getImageById($astrobinId);
                } catch (WsResponseException $e) {
                    $failedAstrobinId[] = $astrobinId;
                    continue;
                }
            }
        }
        $output->write(implode(' - ', $failedAstrobinId));
    }


}
