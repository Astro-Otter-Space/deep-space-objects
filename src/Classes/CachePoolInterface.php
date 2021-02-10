<?php

namespace App\Classes;

/**
 * Interface CacheInterface
 * @package App\Classes
 */
interface CachePoolInterface
{
    /**
     * @param $key
     *
     * @return string|null
     */
    public function getItem(string $key): ?string;

    /**
     * @param $key
     * @param $value
     *
     * @return bool
     */
    public function saveItem(string $key, $value): bool;

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasItem(string $key): bool;

    /**
     * @param $key
     *
     * @return bool
     */
    public function deleteItem(string $key): bool;

    /**
     * @return bool
     */
    public function deleteAll(): bool;
}
