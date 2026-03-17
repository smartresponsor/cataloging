<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Api;

/**
 * Thin API wrapper kept for backward compatibility.
 * Canonical implementation lives in App\Service\CatalogCategoryCacheHeader.
 */
final class CatalogCategoryCacheHeader
{
    private \App\Service\CatalogCategoryCacheHeader $inner;

    public function __construct(?\App\Service\CatalogCategoryCacheHeader $inner = null)
    {
        $this->inner = $inner ?? new \App\Service\CatalogCategoryCacheHeader();
    }

    public function make(string $etag, ?\DateTimeImmutable $lastModified = null): array
    {
        return $this->inner->make($etag, $lastModified);
    }
}
