<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries canonical resolver input for category storefront URLs.
 */
final readonly class CategoryCanonicalResolveRequest
{
    /**
     * @param array<string,mixed> $category
     */
    public function __construct(
        private array $category,
        private string $locale,
    ) {
    }

    /** @return array<string,mixed> */
    public function category(): array
    {
        return $this->category;
    }

    public function locale(): string
    {
        return trim(strtolower($this->locale)) ?: 'en';
    }

    public function slug(): string
    {
        $value = $this->category['slug'] ?? 'category';

        return is_scalar($value) ? trim((string) $value, '/') : 'category';
    }
}
