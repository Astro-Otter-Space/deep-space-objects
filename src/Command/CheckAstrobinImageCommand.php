<?php


namespace App\Command;

use App\Helpers\MailHelper;
use App\Repository\DsoRepository;
use App\Service\MailService;
use Astrobin\Exceptions\WsException;
use Astrobin\Exceptions\WsResponseException;
use Astrobin\Services\GetImage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class CheckAstrobinImageCommand
 * @package App\Command
 */
class CheckAstrobinImageCommand extends Command
{

    protected static $defaultName = "dso:check-astrobin";

    /** @var DsoRepository */
    protected $dsoRepository;

    /** @var MailService */
    protected $mailHelper;

    protected $senderMail;

    /**
     * CheckAstrobinImageCommand constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param MailService $mailHelper
     * @param string $senderMail
     */
    public function __construct(DsoRepository $dsoRepository, MailService $mailHelper, $senderMail)
    {
        $this->dsoRepository = $dsoRepository;
        $this->mailHelper = $mailHelper;
        $this->senderMail = $senderMail;
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
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $failedAstrobinId = [];
        $listAstrobinId = $this->dsoRepository->getAstrobinId([]);
        if (0 < count($listAstrobinId)) {
            foreach ($listAstrobinId as $astrobinId) {
                /** @var GetImage $image */
                $image = new GetImage();

                try {
                    $result = $image->debugImageById($astrobinId);
                } catch (WsException $e) {
                    $failedAstrobinId[] = $astrobinId . ' - ' . $e->getMessage();
                }

                if (property_exists($result, 'http_code') && Response::HTTP_NOT_FOUND === $result->http_code) {
                    $failedAstrobinId[] = $result->data;
                }
            }
        }
        $template = [
            'html' => 'includes/emails/check_astrobin_id.html.twig'
        ];
        $content['listAstrobinId'] = $failedAstrobinId;

        try {
            if (0 < count($failedAstrobinId)) {
                $this->mailHelper->sendMail($this->senderMail, 'Astrobin Id 404', $template, $content);
            }
        } catch (TransportExceptionInterface $e) {
            $output->writeln($e->getMessage());
        }

    }

}
