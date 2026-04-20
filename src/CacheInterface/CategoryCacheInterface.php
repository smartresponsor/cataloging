<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\CacheInterface;

/**
 * Defines the contract for category cache.
 */
interface CategoryCacheInterface
{
    /**
     * Returns the requested value for the provided key.
     */
    public function get(string $key): mixed;

    /**
     * Stores the provided value for the target key.
     */
    public function set(string $key, mixed $value, int $ttl): void;

    /**
     * Removes the cached value for the target key.
     */
    public function del(string $key): void;
}
