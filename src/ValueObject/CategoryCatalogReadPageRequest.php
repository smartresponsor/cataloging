<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the normalized catalog read page request.
 */
final readonly class CategoryCatalogReadPageRequest
{
    public function __construct(
        private int $first = 20,
        private string $after = '',
    ) {
    }

    public function first(): int
    {
        return max(1, min(100, $this->first));
    }

    public function after(): string
    {
        return trim($this->after);
    }
}
