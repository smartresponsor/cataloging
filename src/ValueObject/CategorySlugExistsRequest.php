<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries repository slug collision lookup input.
 */
final readonly class CategorySlugExistsRequest
{
    public function __construct(
        private string $slug,
        private string $taxonomyId,
        private ?string $parentId,
        private string $locale,
    ) {
    }

    public function slug(): string
    {
        return trim($this->slug);
    }

    public function taxonomyId(): string
    {
        return trim($this->taxonomyId);
    }

    public function parentId(): ?string
    {
        return null !== $this->parentId ? trim($this->parentId) : null;
    }

    public function locale(): string
    {
        return trim($this->locale);
    }
}
