<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CategoryCanonicalUrlRequest;

/**
 * Defines the contract for canonical policy.
 */
interface CatalogCanonicalPolicyServiceInterface
{
    /**
     * Handles the url workflow.
     */
    public function url(CategoryCanonicalUrlRequest $request): string;
}
