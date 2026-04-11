<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the full input surface for category publication gate evaluation workflows.
 */
final readonly class CategoryPublicationGateEvaluationRequest
{
    /** @param array<string,bool> $checks */
    public function __construct(
        private string $categoryId,
        private string $workflowState,
        private array $checks,
        private string $actorId,
        private string $reason,
    ) {
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function workflowState(): string
    {
        return $this->workflowState;
    }

    /** @return array<string,bool> */
    public function checks(): array
    {
        return $this->checks;
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
