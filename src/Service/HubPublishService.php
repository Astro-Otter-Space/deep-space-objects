<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;


class HubPublishService
{

    public function __construct(
        HubInterface $hub,
        protected ?string $mercureUrl,
    ) { }
	

    public function publish(
        string $message
    )
    {
        $update = new Update(
	    $this->mercureUrl,
	    json_encode(['message' => $message, 'date' => new \DateTime('now')])
        );

        return $this->hub->publish($update);
    }
}

