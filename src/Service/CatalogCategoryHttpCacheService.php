<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CatalogCategoryHttpCacheServiceInterface;

/**
 * Provides the category http cache application service.
 */
final class CatalogCategoryHttpCacheService implements CatalogCategoryHttpCacheServiceInterface
{
    /** @param list<string> $fieldList */
    public function eTagFor(string $resourceId, array $fieldList): string
    {
        $key = $resourceId.'|'.implode(',', $fieldList);

        return '"'.sha1($key).'"';
    }

    /**
     * Determines whether the not modified condition is satisfied.
     */
    public function isNotModified(string $eTag, string $ifNoneMatch): bool
    {
        return $ifNoneMatch === $eTag;
    }
}
