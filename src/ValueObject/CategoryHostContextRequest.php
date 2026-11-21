<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries host-scoped storefront resolution input.
 */
final readonly class CategoryHostContextRequest
{
    /**
     * Initializes host context request state.
     */
    public function __construct(private string $host)
    {
    }

    public function host(): string
    {
        return trim(strtolower($this->host));
    }
}
