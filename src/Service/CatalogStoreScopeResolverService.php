<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ValueObject\CategoryHostContextRequest;

/**
 * Provides the store scope resolver application service.
 */
final class CatalogStoreScopeResolverService
{
    /**
     * Resolves the requested result for the provided input.
     */
    public function resolve(CategoryHostContextRequest $request): string
    {
        return 'merchant.local' === $request->host() ? 'merchant' : 'default';
    }
}
