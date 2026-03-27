<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface CategoryHttpCacheInterface
{
    /** @param list<string> $fieldList */
    public function eTagFor(string $resourceId, array $fieldList): string;

    public function isNotModified(string $eTag, string $ifNoneMatch): bool;
}
