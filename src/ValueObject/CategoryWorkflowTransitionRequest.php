<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category workflow transition workflows.
 */
final readonly class CategoryWorkflowTransitionRequest
{
    public function __construct(
        private string $categoryId,
        private string $targetState,
        private string $actorId,
        private string $reason,
    ) {
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function targetState(): string
    {
        return $this->targetState;
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
