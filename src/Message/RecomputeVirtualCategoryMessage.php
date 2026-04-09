<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Message;

/**
 * Provides the recompute virtual category message implementation.
 */
final readonly class RecomputeVirtualCategoryMessage
{
    /**
     * Initializes the recompute virtual category message service collaborators.
     */
    public function __construct(public string $virtualCategoryId)
    {
    }
}
