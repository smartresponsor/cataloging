<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries localized slug generation input.
 */
final readonly class CategorySlugGenerationRequest
{
    /**
     * @param array<string, string> $input
     */
    public function __construct(
        private array $input,
        private string $taxonomyId,
        private ?string $parentId,
    ) {
    }

    /** @return array<string, string> */
    public function input(): array
    {
        return $this->input;
    }

    public function taxonomyId(): string
    {
        return trim($this->taxonomyId);
    }

    public function parentId(): ?string
    {
        return null !== $this->parentId ? trim($this->parentId) : null;
    }
}
