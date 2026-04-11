<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ValueObject\CategoryCanonicalUrlRequest;

/**
 * Provides the canonical policy application service.
 */
final class CanonicalPolicy
{
    /**
     * Handles the url workflow.
     */
    public function url(CategoryCanonicalUrlRequest $request): string
    {
        return $request->host().'/'.$request->locale().'/'.$request->slug();
    }
}
