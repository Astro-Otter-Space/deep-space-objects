<?php

namespace App\EventListener;

use App\Entity\ApiUser;
use App\Helpers\MailHelper;
use App\Service\CurlService;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


/**
 * Class GenerateTokenListener
 * Get Bearer token from new user and sending him by email
 * @package App\EventListener
 */
class GenerateTokenListener
{
    /** @var CurlService  */
    private $curl;

    /** @var MailHelper */
    private $mailHelper;

    /** @var string */
    private $senderMail;

    /**
     * GenerateTokenListener constructor.
     *
     * @param CurlService $curl
     * @param MailHelper $mailHelper
     * @param string $senderMail
     */
    public function __construct(CurlService $curl, MailHelper $mailHelper, string $senderMail)
    {
        $this->curl = $curl;
        $this->mailHelper = $mailHelper;
        $this->senderMail = $senderMail;
    }

    /**
     * @param ApiUser $apiUser
     * @param LifecycleEventArgs $event
     *
     * @throws TransportExceptionInterface
     * @throws \Swift_TransportException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function postPersist(ApiUser $apiUser, LifecycleEventArgs $event)
    {
        try {
            /** @var ResponseInterface $responseToken */
            $responseToken = $this->curl->getBearerToken($apiUser);
        } catch(ClientException $e) {
            dump($e->getMessage());
        }

        dump($responseToken->getStatusCode(), $responseToken->getContent(), $responseToken->getInfo()); die();
        if (Response::HTTP_OK == $responseToken->getStatusCode()) {
            $from = $this->senderMail;
            $to = $apiUser->getEmail();
            $subject = 'API Bearer Token';

            $template = [
                'html' => 'includes/emails/api_register.html.twig',
                'text' => 'includes/emails/api_register.txt.twig'
            ];

            $data['token'] = $responseToken->getContent();

            $this->mailHelper->sendMail($from, $to, $subject, $template, $data);
        }

    }
}
