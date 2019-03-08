<?php


namespace App\Classes;

/**
 * Interface CacheInterface
 * @package App\Classes
 */
interface CacheInterface
{
    public function getItem($key): ?string;

    public function saveItem($key, $value): bool;

    public function hasItem($key): bool;

    public function deleteItem($key): bool;

    public function deleteAll(): bool;
}
