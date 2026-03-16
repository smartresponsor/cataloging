<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cache;

use App\CacheInterface\testsCacheInterface;

/** Simple array cache; real impl in infra (Redis/Memcached). */
final class testsCache implements testsCacheInterface
{
    /** @var array<string, array{v:mixed, exp:int}> */
    private array $s = [];

    public function get(string $key): mixed
    {
        if (!isset($this->s[$key])) {
            return null;
        }
        if ($this->s[$key]['exp'] < time()) {
            unset($this->s[$key]);

            return null;
        }

        return $this->s[$key]['v'];
    }

    public function set(string $key, mixed $value, int $ttl): void
    {
        $this->s[$key] = ['v' => $value, 'exp' => time() + $ttl];
    }

    public function del(string $key): void
    {
        unset($this->s[$key]);
    }
}
