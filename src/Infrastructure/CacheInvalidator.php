<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Infrastructure;

use Psr\Cache\CacheItemPoolInterface;

final class CacheInvalidator
{
    public function __construct(private readonly CacheItemPoolInterface $pool)
    {
    }

    public function invalidateSlug(string $slug): void
    {
        $this->pool->deleteItem('category_'.$slug);
    }
}
