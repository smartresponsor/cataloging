<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\Service;

final class CategoryHttpCache implements CategoryHttpCacheInterface
{
    public function eTagFor(string $resourceId, array $fieldList): string
    {
        $key = $resourceId.'|'.implode(',', $fieldList);

        return '"'.sha1($key).'"';
    }

    public function isNotModified(string $eTag, string $ifNoneMatch): bool
    {
        return $ifNoneMatch === $eTag;
    }
}
