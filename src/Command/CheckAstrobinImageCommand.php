<?php

//declare(strict_types=1);

namespace App\Command;

use App\Repository\DsoRepository;
use App\Service\MailService;
use AstrobinWs\Exceptions\WsException;
use AstrobinWs\Response\AstrobinResponse;
use AstrobinWs\Services\GetImage;
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
    protected DsoRepository $dsoRepository;
    protected MailService $mailHelper;
    protected string $senderMail;
    protected string $receiverMail;

    /**
     * CheckAstrobinImageCommand constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param MailService $mailHelper
     * @param string $senderMail
     * @param string $receiverMail
     */
    public function __construct(DsoRepository $dsoRepository, MailService $mailHelper, string $senderMail,string $receiverMail)
    {
        $this->dsoRepository = $dsoRepository;
        $this->mailHelper = $mailHelper;
        $this->senderMail = $senderMail;
        $this->receiverMail = $receiverMail;
        parent::__construct();
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('return Astrobin ImageId failed');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $failedAstrobinId = [];
        $listDsoAstrobin = $this->dsoRepository->getAstrobinId(null);
        if (0 < count($listDsoAstrobin)) {
            foreach ($listDsoAstrobin as $dsoId => $astrobinId) {
                /** @var GetImage $image */
                $image = new GetImage();

                try {
                    /** @var AstrobinResponse $result */
                    $result = $image->getById((string)$astrobinId);
                } catch (WsException $e) {
                    $failedAstrobinId[Response::HTTP_NOT_FOUND][$dsoId] = $astrobinId;
                }
            }
        }

        $template = [
            'html' => 'includes/emails/check_astrobin_id.html.twig'
        ];
        $content['listAstrobinId'] = $failedAstrobinId;

        /** @var \DateTimeInterface $now */
        $now = new \DateTime();
        $subject = sprintf('%s - Astrobin Id 404', $now->format('Y-m-d'));

        try {
            if (0 < count($failedAstrobinId)) {
                $this->mailHelper->sendMail($this->senderMail, $this->receiverMail, $subject, $template, $content);
            }
            return Command::SUCCESS;
        } catch (TransportExceptionInterface $e) {
            $output->writeln($e->getMessage());

        }
        return Command::FAILURE;
    }
}
