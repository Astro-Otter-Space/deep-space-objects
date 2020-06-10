<?php

namespace App\EventListener;

use App\Entity\BDD\ApiUser;
use App\Service\MailService;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
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

    /** @var JWTTokenManagerInterface */
    private $jwtManager;
    /** @var MailService  */
    private $mailService;
    /** @var TranslatorInterface */
    private $translator;
    /** @var string */
    private $senderMail;

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
     * @throws TransportExceptionInterface
     */
    public function postPersist(ApiUser $apiUser, LifecycleEventArgs $event): void
    {
        $to = $apiUser->getEmail();
        $subject = sprintf('[API %s] Here\'s your bearer Token', $this->translator->trans('dso'));

        $template = [
            'html' => 'includes/emails/api_register.html.twig',
            'text' => 'includes/emails/api_register.txt.twig'
        ];

        $data['token'] = $this->jwtManager->create($apiUser);
        $data['refresh_token'] = 'IN PROGRESS';

        $this->mailService->sendMail($this->senderMail, $to, $subject, $template, $data);
    }
}
