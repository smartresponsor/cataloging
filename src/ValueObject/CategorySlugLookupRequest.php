<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category slug lookup workflows.
 */
final readonly class CategorySlugLookupRequest
{
    public function __construct(
        private string $taxonomyCode,
        private string $slug,
        private string $locale,
    ) {
    }

    public function taxonomyCode(): string
    {
        return $this->taxonomyCode;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function locale(): string
    {
        return $this->locale;
    }
}
