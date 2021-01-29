<?php

namespace App\EventListener;

use App\Entity\BDD\ApiUser;
use App\Service\MailService;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Gesdinet\JWTRefreshTokenBundle\Event\RefreshEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class GenerateTokenListener
 * Get Bearer token from new user and sending him by email
 * @package App\EventListener
 */
class GenerateTokenListener
{
    private JWTTokenManagerInterface $jwtManager;
    private MailService $mailService;
    private TranslatorInterface $translator;
    private string $senderMail;

    /**
     * GenerateTokenListener constructor.
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param MailService $mailService
     * @param TranslatorInterface $translator
     * @param string $senderMail
     */
    public function __construct(JWTTokenManagerInterface $jwtManager, MailService $mailService, TranslatorInterface $translator, string $senderMail)
    {
        $this->jwtManager = $jwtManager;
        $this->mailService = $mailService;
        $this->translator = $translator;
        $this->senderMail = $senderMail;
    }

    /**
     * @param ApiUser $apiUser
     * @param LifecycleEventArgs $event
     *
     * @param RefreshEvent $refreshEvent
     *
     * @throws TransportExceptionInterface
     */
    public function postPersist(ApiUser $apiUser, LifecycleEventArgs $event, RefreshEvent $refreshEvent): void
    {
        $to = $apiUser->getEmail();
        $subject = sprintf('[API %s] Here\'s your bearer Token', $this->translator->trans('dso'));

        $template = [
            'html' => 'includes/emails/api_register.html.twig',
            'text' => 'includes/emails/api_register.txt.twig'
        ];

        $data['token'] = $this->jwtManager->create($apiUser);
        $data['refresh_token'] = $refreshEvent->getRefreshToken()->getRefreshToken();

        $this->mailService->sendMail($this->senderMail, $to, $subject, $template, $data);
    }
}
