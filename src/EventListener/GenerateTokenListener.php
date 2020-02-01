<?php

namespace App\EventListener;

use App\Entity\BDD\ApiUser;
use App\Helpers\MailHelper;
use App\Service\MailService;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;


/**
 * Class GenerateTokenListener
 * Get Bearer token from new user and sending him by email
 * @package App\EventListener
 */
class GenerateTokenListener
{

    /** @var JWTTokenManagerInterface */
    private $jwtManager;
    /** @var MailHelper */
    private $mailHelper;
    /** @var MailService  */
    private $mailService;
    /** @var string */
    private $senderMail;

    /**
     * GenerateTokenListener constructor.
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param MailService $mailService
     * @param MailHelper $mailHelper
     * @param string $senderMail
     */
    public function __construct(JWTTokenManagerInterface $jwtManager, MailService $mailService, MailHelper $mailHelper, string $senderMail)
    {
        $this->jwtManager = $jwtManager;
        $this->mailService = $mailService;
        $this->mailHelper = $mailHelper;
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
        $subject = 'API Bearer Token';

        $template = [
            'html' => 'includes/emails/api_register.html.twig',
            'text' => 'includes/emails/api_register.txt.twig'
        ];

        $data['token'] = $this->jwtManager->create($apiUser);

        //$this->mailHelper->sendMail($from, $to, $subject, $template, $data);
        $this->mailService->sendMail($to, $subject, $template, $data);
    }
}
