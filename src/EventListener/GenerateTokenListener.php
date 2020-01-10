<?php

namespace App\EventListener;

use App\Entity\ApiUser;
use App\Helpers\MailHelper;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


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

    /** @var string */
    private $senderMail;

    /**
     * GenerateTokenListener constructor.
     *
     * @param JWTTokenManagerInterface $jwtManager
     * @param MailHelper $mailHelper
     * @param string $senderMail
     */
    public function __construct(JWTTokenManagerInterface $jwtManager, MailHelper $mailHelper, string $senderMail)
    {
        $this->jwtManager = $jwtManager;
        $this->mailHelper = $mailHelper;
        $this->senderMail = $senderMail;
    }

    /**
     * @param ApiUser $apiUser
     * @param LifecycleEventArgs $event
     *
     * @throws \Swift_TransportException
     */
    public function postPersist(ApiUser $apiUser, LifecycleEventArgs $event): void
    {
        $from = $this->senderMail;
        $to = $apiUser->getEmail();
        $subject = 'API Bearer Token';

        $template = [
            'html' => 'includes/emails/api_register.html.twig',
            'text' => 'includes/emails/api_register.txt.twig'
        ];

        $data['token'] = $this->jwtManager->create($apiUser);

        $this->mailHelper->sendMail($from, $to, $subject, $template, $data);
    }
}
