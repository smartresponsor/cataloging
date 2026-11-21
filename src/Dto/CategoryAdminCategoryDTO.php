<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Dto;

/**
 * Carries normalized category administration form data.
 */
final class CategoryAdminCategoryDTO
{
    public function __construct(
        public string $nameEntity = '',
        public string $slug = '',
    ) {
    }

    /** @param array{nameEntity?:mixed,slug?:mixed} $payload */
    public static function fromArray(array $payload): self
    {
        return new self(
            is_scalar($payload['nameEntity'] ?? null) ? trim((string) $payload['nameEntity']) : '',
            is_scalar($payload['slug'] ?? null) ? trim((string) $payload['slug']) : '',
        );
    }
}
