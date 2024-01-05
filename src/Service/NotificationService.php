<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;


class NotificationService
{

    private static string $topic = 'notifications/all';

    public function __construct(
        HubInterface $hub,
        protected ?string $mercureUrl,
    ) { }
	

    public function send(
        string $message
    )
    {
        $update = new Update(
	    sprintf('%s/%s', $this->mercureUrl, self::$topic),
	    json_encode(['message' => $message, 'date' => new \DateTime('now')])
        );

        return $this->hub->publish($update);
    }
}

