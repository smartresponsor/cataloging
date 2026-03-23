<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cache;

use App\CacheInterface\CategoryCacheInterface;

final class CategoryCache implements CategoryCacheInterface
{
    /** @var array<string, array{v:mixed, exp:int}> */
    private array $entries = [];

    public function get(string $key): mixed
    {
        $this->pruneExpired();

        if (!isset($this->entries[$key])) {
            return null;
        }

        return $this->entries[$key]['v'];
    }

    public function set(string $key, mixed $value, int $ttl): void
    {
        $effectiveTtl = max(1, $ttl);
        $this->entries[$key] = [
            'v' => $value,
            'exp' => time() + $effectiveTtl,
        ];
    }

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
