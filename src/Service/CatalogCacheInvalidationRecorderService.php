<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the cache invalidation recorder application service.
 */
final class CatalogCacheInvalidationRecorderService
{
    /**
     * Handles the invalidate workflow.
     */
    public function invalidate(int|string $id): void
    {
        $key = 'category:'.$id;
        file_put_contents('report/category-cache-invalidated.log', $key."\n", flags: FILE_APPEND);
    }
}
