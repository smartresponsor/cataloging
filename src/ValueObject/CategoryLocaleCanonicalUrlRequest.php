<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries locale-scoped canonical URL input.
 */
final readonly class CategoryLocaleCanonicalUrlRequest
{
    /**
     * Initializes locale canonical URL request state.
     */
    public function __construct(
        private string $locale,
        private string $slug,
    ) {
    }

    public function locale(): string
    {
        return trim(strtolower($this->locale)) ?: 'en';
    }

    public function slug(): string
    {
        return trim($this->slug, '/');
    }
}
