<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

/**
 * Class CacheKernel
 *
 * @package App
 */
class CacheKernel extends HttpCache
{
    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            'default_ttl' => 31556952
        ];
    }
}
