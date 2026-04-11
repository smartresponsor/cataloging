<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries canonical URL policy input.
 */
final readonly class CategoryCanonicalUrlRequest
{
    /**
     * Initializes canonical URL request state.
     */
    public function __construct(
        private string $host,
        private string $locale,
        private string $slug,
    ) {
    }

    public function host(): string
    {
        return rtrim(trim($this->host), '/');
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
