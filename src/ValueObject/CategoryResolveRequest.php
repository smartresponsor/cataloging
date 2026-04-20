<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Typed category target resolution request.
 */
final readonly class CategoryResolveRequest
{
    public function __construct(
        private string $taxonomyCode,
        private string $targetDomain,
        private string $targetId,
        private string $locale,
    ) {
    }

    public function taxonomyCode(): string
    {
        return $this->taxonomyCode;
    }

    public function targetDomain(): string
    {
        return $this->targetDomain;
    }

    public function targetId(): string
    {
        return $this->targetId;
    }

    public function locale(): string
    {
        return $this->locale;
    }
}
