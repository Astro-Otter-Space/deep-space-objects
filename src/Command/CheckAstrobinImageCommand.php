<?php


namespace App\Command;


use App\Repository\DsoRepository;
use Astrobin\Exceptions\WsResponseException;
use Astrobin\Response\Image;
use Astrobin\Services\GetImage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

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
        $failedAstrobinId = [];
        $listAstrobinId = $this->dsoRepository->getAstrobinId();
        if (0 < count($listAstrobinId)) {
            foreach ($listAstrobinId as $astrobinId) {
                /** @var GetImage $image */
                $image = new GetImage();

                $result = $image->debugImageById($astrobinId);
                if (property_exists($result, 'http_code') && Response::HTTP_NOT_FOUND === $result->http_code) {
                    $failedAstrobinId[] = $result->data;
                }
            }
        }
        $output->write(implode(' - ', $failedAstrobinId));
    }


}
