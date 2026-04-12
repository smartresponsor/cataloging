<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cache;

use App\CacheInterface\CategoryCacheInterface;

/**
 * Provides the category cache implementation.
 */
final class CategoryCache implements CategoryCacheInterface
{
    /** @var array<string, array{v:mixed, exp:int}> */
    private array $entries = [];

    /**
     * Returns the requested value for the provided key.
     */
    public function get(string $key): mixed
    {
        $this->pruneExpired();

        if (!isset($this->entries[$key])) {
            return null;
        }

        return $this->entries[$key]['v'];
    }

    /**
     * Stores the provided value for the target key.
     */
    public function set(string $key, mixed $value, int $ttl): void
    {
        $effectiveTtl = max(1, $ttl);
        $this->entries[$key] = [
            'v' => $value,
            'exp' => time() + $effectiveTtl,
        ];
    }

    /**
     * Removes the cached value for the target key.
     */
    public function del(string $key): void
    {
        unset($this->entries[$key]);
    }

    private function pruneExpired(): void
    {
        $now = time();
        foreach ($this->entries as $key => $entry) {
            if ($entry['exp'] < $now) {
                unset($this->entries[$key]);
            }
        }
    }
}
