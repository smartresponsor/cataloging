<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\ServiceInterface;

interface CatalogCategoryHttpCacheInterface
{
    public function eTagFor(string $resourceId, array $fieldList): string;

    public function isNotModified(string $eTag, string $ifNoneMatch): bool;
}
