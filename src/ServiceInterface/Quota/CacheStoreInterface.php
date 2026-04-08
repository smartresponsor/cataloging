<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Quota;
/**
 * Defines the contract for cache store.
 */
interface CacheStoreInterface
{
    /**
     * Returns the requested value for the provided key.
     */
    public function get(string $key): ?string;
    /**
     * Stores the provided value for the target key.
     */
    public function set(string $key, string $value, int $ttl): void;
}
