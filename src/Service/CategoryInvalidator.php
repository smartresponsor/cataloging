<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Provides the category invalidator application service.
 */
final readonly class CategoryInvalidator
{
    /**
     * Initializes the category invalidator service collaborators.
     */
    public function __construct(private CacheItemPoolInterface $pool)
    {
    }

    /**
     * Handles the invalidate all workflow.
     */
    public function invalidateAll(): void
    {
        $this->pool->clear();
    }
}
