<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Dto;
/**
 * Provides the category admin category data implementation.
 */
final class CategoryAdminCategoryData
{
    /**
     * Initializes the category admin category data service collaborators.
     */
    public function __construct(
        public string $name = '',
        public string $slug = '',
    ) {
    }

    /** @param array{name?:mixed,slug?:mixed} $payload */
    public static function fromArray(array $payload): self
    {
        return new self(
            is_scalar($payload['name'] ?? null) ? trim((string) $payload['name']) : '',
            is_scalar($payload['slug'] ?? null) ? trim((string) $payload['slug']) : '',
        );
    }
}
