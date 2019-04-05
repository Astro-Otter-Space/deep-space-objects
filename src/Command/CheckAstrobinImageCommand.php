<?php


namespace App\Command;

use App\Helpers\MailHelper;
use App\Repository\DsoRepository;
use Astrobin\Exceptions\WsResponseException;
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

    /** @var MailHelper */
    protected $mailHelper;

    /**
     * CheckAstrobinImageCommand constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param MailHelper $mailHelper
     */
    public function __construct(DsoRepository $dsoRepository, MailHelper $mailHelper)
    {
        $this->dsoRepository = $dsoRepository;
        $this->mailHelper = $mailHelper;
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
     * @throws WsResponseException
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
        $output->writeln(dump($failedAstrobinId));
        $template = [
            'html' => 'includes/emails/check_astrobin_id.html.twig'
        ];
        $content['listAstrobinId'] = $failedAstrobinId;

        try {
            $this->mailHelper->sendMail('noreply@deepskyobjects.com', 'balistik.fonfon@gmail.com', 'Astrobin Id 404', $template, $content);
        } catch (\Swift_TransportException $e) {
            $output->writeln($e->getMessage());
        }

    }


}