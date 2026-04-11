<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries repository-level category creation input.
 */
final readonly class CategoryRepositoryCreateRequest
{
    /**
     * @param array<string, scalar|null> $name
     * @param array<string, string>      $slug
     * @param array<string, mixed>       $meta
     */
    public function __construct(
        private string $taxonomyId,
        private ?string $parentId,
        private array $name,
        private array $slug,
        private array $meta = [],
    ) {
    }

    public function taxonomyId(): string
    {
        return trim($this->taxonomyId);
    }

    public function parentId(): ?string
    {
        return null !== $this->parentId ? trim($this->parentId) : null;
    }

    /** @return array<string, scalar|null> */
    public function name(): array
    {
        return $this->name;
    }

    /** @return array<string, string> */
    public function slug(): array
    {
        return $this->slug;
    }

    /** @return array<string, mixed> */
    public function meta(): array
    {
        return $this->meta;
    }
}
