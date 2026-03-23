<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;

final class CategoryInvalidator
{
    public function __construct(private readonly CacheItemPoolInterface $pool)
    {
    }

    public function invalidateAll(): void
    {
        $this->pool->clear();
    }
}
