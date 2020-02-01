<?php

namespace App\Service;

use App\Classes\CacheInterface;
use Symfony\Contracts\Cache\CacheInterface as SfCacheInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

/**
 * Class CachepoolService
 *
 * @package App\Service
 * @source http://www.inanzzz.com/index.php/post/ruhe/symfony-memcached-and-redis-adapter-as-cache-pool
 */
class CachepoolService implements CacheInterface
{
    /** @var MemcachedAdapter  */
    private $cachePool;

    /**
     * CachepoolService constructor.
     *
     * @param SfCacheInterface $cachePool
     */
    public function __construct(SfCacheInterface $cachePool)
    {
        $this->cachePool = $cachePool;
    }

    /**
     * @param $key
     *
     * @return bool|mixed
     * @throws InvalidArgumentException
     */
    public function getItem($key): string
    {
        $cacheItem = $this->cachePool->getItem($key);

        return ($cacheItem->isHit()) ? $cacheItem->get(): false;
    }


    /**
     * @param $key
     * @param $value
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function saveItem($key, $value): bool
    {
        $cacheItem = $this->cachePool->getItem($key);
        $cacheItem->set($value);

        return $this->cachePool->save($cacheItem);
    }

    /**
     * @param $key
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function hasItem($key): bool
    {
        return $this->cachePool->hasItem($key);
    }

    /**
     * @param $key
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function deleteItem($key): bool
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
