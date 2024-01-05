<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;


class HubPublishService
{

	public function __construct(
		protected ?string $mercureUrl,
		private HubInterface $hub
	) { }
	

	public function publish(
		string $message
	): void
	{
		$update = new Update(
			$this->mercureUrl,
			json_encode(['message' => $message, 'date' => new DateTime('now')])
		);
		
		try { 
			$this->hub->publish($update);
		} catch (\Exception $e) { }
		
	}
	
}
