<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

/**
 * Defines the contract for category http cache.
 */
interface CategoryHttpCacheInterface
{
    /** @param list<string> $fieldList */
    public function eTagFor(string $resourceId, array $fieldList): string;

    /**
     * Determines whether the not modified condition is satisfied.
     */
    public function isNotModified(string $eTag, string $ifNoneMatch): bool;
}
