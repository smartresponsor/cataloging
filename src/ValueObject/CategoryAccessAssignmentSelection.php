<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries category and actor coordinates for access assignment selection workflows.
 */
final readonly class CategoryAccessAssignmentSelection
{
    /**
     * Initializes the category access assignment selection value object.
     */
    public function __construct(
        private string $categoryId,
        private string $actorUserId,
    ) {
    }

    public function categoryId(): string
    {
        return trim($this->categoryId);
    }

    public function actorUserId(): string
    {
        return trim($this->actorUserId);
    }
}
