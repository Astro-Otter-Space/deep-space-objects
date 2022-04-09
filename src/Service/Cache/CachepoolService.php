<?php

declare(strict_types=1);

namespace App\Service\Cache;

use Psr\Cache\InvalidArgumentException;
//use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Contracts\Cache\CacheInterface as AbstractAdapter;

/**
 * Class CachepoolService
 *
 * @package App\Service
 * @source http://www.inanzzz.com/index.php/post/ruhe/symfony-memcached-and-redis-adapter-as-cache-pool
 */
final class CachepoolService implements CachePoolInterface
{
    private AbstractAdapter $cachePool;

    /**
     * CachepoolService constructor.
     *
     * @param AbstractAdapter $cachePool
     */
    public function __construct(AbstractAdapter $cachePool)
    {
        $this->cachePool = $cachePool;
    }

    /**
     * @param string $key
     *
     * @return bool|mixed
     */
    public function getItem(string $key): ?string
    {
        $cacheItem = $this->cachePool->getItem($key);
        return ($cacheItem->isHit()) ? $cacheItem->get(): null;
    }


    /**
     * @param string $key
     * @param $value
     *
     * @return bool
     */
    public function saveItem(string $key, $value): bool
    {
        $cacheItem = $this->cachePool->getItem($key);
        $cacheItem->set($value);

        return $this->cachePool->save($cacheItem);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasItem(string $key): bool
    {
        return $this->cachePool->hasItem($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function deleteItem(string $key): bool
    {
        return $this->cachePool->deleteItem($key);
    }

    /**
     * @return bool
     */
    public function deleteAll(): bool
    {
        return $this->cachePool->clear();
    }
}
