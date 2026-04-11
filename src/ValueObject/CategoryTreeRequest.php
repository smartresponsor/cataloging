<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Typed category tree request for controller/repository coordination.
 */
final readonly class CategoryTreeRequest
{
    public function __construct(
        private string $taxonomyCode,
        private ?string $parentId,
        private int $depth,
        private string $locale,
    ) {
    }

    public function taxonomyCode(): string
    {
        return $this->taxonomyCode;
    }

    public function parentId(): ?string
    {
        return $this->parentId;
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function locale(): string
    {
        return $this->locale;
    }
}
