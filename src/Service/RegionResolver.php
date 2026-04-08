<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the region resolver application service.
 */
final class RegionResolver
{
    /**
     * Resolves the requested result for the provided input.
     */
    public function resolve(string $host): string
    {
        return str_contains($host, 'eu') ? 'eu' : 'us';
    }
}
