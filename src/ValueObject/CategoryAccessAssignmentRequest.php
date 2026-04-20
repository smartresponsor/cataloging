<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category access assignment workflows.
 */
final readonly class CategoryAccessAssignmentRequest
{
    /**
     * Initializes the category access assignment request value object.
     */
    public function __construct(
        private string $categoryId,
        private string $actorUserId,
        private string $role,
        private bool $isPrimary = false,
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

    public function role(): string
    {
        return trim($this->role);
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }
}
