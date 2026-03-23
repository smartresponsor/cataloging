<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Quota;

interface CacheStoreInterface
{
    public function get(string $key): ?string;

    public function set(string $key, string $value, int $ttl): void;
}
