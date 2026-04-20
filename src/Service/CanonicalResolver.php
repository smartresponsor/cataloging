<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ValueObject\CategoryCanonicalResolveRequest;

/**
 * Provides the canonical resolver application service.
 */
final class CanonicalResolver
{
    public function resolve(CategoryCanonicalResolveRequest $request): string
    {
        return '/'.$request->locale().'/category/'.$request->slug();
    }
}
