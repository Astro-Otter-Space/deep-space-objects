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
    ) { 
        $this->hub = $hub;
    }
	

    public function send(
        string $message
    )
    {
        $update = new Update(
	    sprintf('%s/%s', 'https://api.astro-otter.space', self::$topic),
	    json_encode(['message' => $message, 'date' => new \DateTime('now')])
        );

        return $this->hub->publish($update);
    }
}

