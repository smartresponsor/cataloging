<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CategoryCanonicalUrlRequest;

/**
 * Defines the contract for canonical policy.
 */
interface CanonicalPolicyInterface
{
    /**
     * Handles the url workflow.
     */
    public function url(CategoryCanonicalUrlRequest $request): string;
}
