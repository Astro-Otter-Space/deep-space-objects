<?php


namespace App\Classes;

/**
 * Interface CacheInterface
 * @package App\Classes
 */
interface CacheInterface
{
    /**
     * @param $key
     *
     * @return string|null
     */
    public function getItem($key): ?string;

    /**
     * @param $key
     * @param $value
     *
     * @return bool
     */
    public function saveItem($key, $value): bool;

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasItem($key): bool;


    /**
     * @param $key
     *
     * @return bool
     */
    public function deleteItem($key): bool;

    /**
     * @return bool
     */
    public function deleteAll(): bool;
}
