<?php

namespace App\EventListener;

use App\Entity\ApiUser;
use App\Helpers\MailHelper;
use App\Service\Curl;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


/**
 * Class GenerateTokenListener
 * Get Bearer token from new user and sending him by email
 * @package App\EventListener
 */
class GenerateTokenListener
{
    /** @var Curl  */
    private $curl;

    /** @var MailHelper */
    private $mailHelper;

    /** @var string */
    private $senderMail;

    /**
     * GenerateTokenListener constructor.
     *
     * @param Curl $curl
     * @param MailHelper $mailHelper
     * @param string $senderMail
     */
    public function __construct(Curl $curl, MailHelper $mailHelper, string $senderMail)
    {
        $this->curl = $curl;
        $this->mailHelper = $mailHelper;
        $this->senderMail = $senderMail;
    }

    /**
     * @param ApiUser $apiUser
     * @param LifecycleEventArgs $event
     *
     * @throws \Swift_TransportException
     * @throws TransportExceptionInterface
     */
    public function postUpdateHandler(ApiUser $apiUser, LifecycleEventArgs $event)
    {
        $token = $this->curl->getBearerToken($apiUser);

        $from = $this->senderMail;
        $to = $apiUser->getEmail();
        $subject = 'API Bearer Token';

        $template = [
            'html' => 'includes/emails/api_register.html.twig',
            'text' => 'includes/emails/api_register.txt.twig'
        ];

        $data['token'] = $token;

        $this->mailHelper->sendMail($from, $to, $subject, $template, $data);
    }
}
