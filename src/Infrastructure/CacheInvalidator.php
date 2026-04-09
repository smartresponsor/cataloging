<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Provides the cache invalidator implementation.
 */
final readonly class CacheInvalidator
{
    /**
     * Initializes the cache invalidator service collaborators.
     */
    public function __construct(private CacheItemPoolInterface $pool)
    {
    }

    /**
     * Handles the invalidate slug workflow.
     */
    public function invalidateSlug(string $slug): void
    {
        $this->pool->deleteItem('category_'.$slug);
    }
}
