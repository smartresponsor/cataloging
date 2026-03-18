<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryReviewDecisionCouplingResultInterface;

final class CategoryReviewDecisionCouplingResult implements CategoryReviewDecisionCouplingResultInterface
{
    /**
     * @param list<string>       $blockers
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     */
    public function __construct(
        private readonly string $requestId,
        private readonly string $categoryId,
        private readonly string $reviewState,
        private readonly string $workflowState,
        private readonly bool $publishable,
        private readonly array $blockers,
        private readonly array $warnings,
        private readonly array $checks,
        private readonly string $actorId,
        private readonly string $reason,
    ) {
    }

    public function requestId(): string
    {
        return $this->requestId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function reviewState(): string
    {
        return $this->reviewState;
    }

    public function workflowState(): string
    {
        return $this->workflowState;
    }

    public function publishable(): bool
    {
        return $this->publishable;
    }

    public function blockers(): array
    {
        return $this->blockers;
    }

    public function warnings(): array
    {
        return $this->warnings;
    }

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

    public function payload(): array
    {
        return [
            'requestId' => $this->requestId,
            'categoryId' => $this->categoryId,
            'reviewState' => $this->reviewState,
            'workflowState' => $this->workflowState,
            'publishable' => $this->publishable,
            'blockers' => $this->blockers,
            'warnings' => $this->warnings,
            'checks' => $this->checks,
            'actorId' => $this->actorId,
            'reason' => $this->reason,
        ];
    }
}
