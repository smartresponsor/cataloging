<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the normalized catalog read node identifier.
 */
final readonly class CategoryCatalogReadNodeRequest
{
    public function __construct(private string $id)
    {
    }

    public function id(): string
    {
        return trim($this->id);
    }
}
