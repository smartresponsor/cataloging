<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ValueObject\CategoryHostContextRequest;

/**
 * Provides the region resolver application service.
 */
final class RegionResolver
{
    /**
     * Resolves the requested result for the provided input.
     */
    public function resolve(CategoryHostContextRequest $request): string
    {
        return str_contains($request->host(), 'eu') ? 'eu' : 'us';
    }
}
