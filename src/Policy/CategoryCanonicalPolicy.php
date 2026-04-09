<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryCanonicalPolicyInterface;
/**
 * Provides the category canonical policy implementation.
 */
final class CategoryCanonicalPolicy implements CategoryCanonicalPolicyInterface
{
    /**
     * Determines whether the current workflow can onical locale.
     */
    public function canonicalLocale(): string
    {
        return 'en';
    }
    /**
     * Creates the redirect result for the current workflow.
     */
    public function createRedirect(): bool
    {
        return true;
    }
}
