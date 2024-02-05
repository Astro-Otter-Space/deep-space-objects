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
        array $message
    ): string
    {
        $update = new Update(
            sprintf('%s/%s', 'https://api.astro-otter.space', self::$topic),
            json_encode($message)
        );

        return $this->hub->publish($update);
    }
}

