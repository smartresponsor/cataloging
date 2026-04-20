<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category review queue workflows.
 */
final readonly class CategoryReviewQueueRequest
{
    /**
     * Initializes the category review queue request value object.
     */
    public function __construct(private string $reviewer)
    {
    }

    public function reviewer(): string
    {
        return trim($this->reviewer);
    }
}
