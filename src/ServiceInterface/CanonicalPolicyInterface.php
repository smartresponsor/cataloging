<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;
/**
 * Defines the contract for canonical policy.
 */
interface CanonicalPolicyInterface
{
    /**
     * Handles the url workflow.
     */
    public function url(string $host, string $locale, string $slug): string;
}
