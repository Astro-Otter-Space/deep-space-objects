<?php

namespace App\Service\SocialNetworks\WebServices;

use App\Service\SocialNetworks\Singleton\Facebook;

/**
 * Interface socialNetworkInterface
 */
interface socialNetworkInterface
{
    /**  */
    public function buildFactory(): void;

    /**
     * @param array $body
     */
    public function buildResponse(array $body);
}
