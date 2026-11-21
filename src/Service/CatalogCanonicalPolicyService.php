<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ValueObject\CategoryCanonicalUrlRequest;

/**
 * Provides the canonical policy application service.
 */
final class CatalogCanonicalPolicyService
{
    /**
     * Handles the url workflow.
     */
    public function url(CategoryCanonicalUrlRequest $request): string
    {
        return $request->host().'/'.$request->locale().'/'.$request->slug();
    }
}
