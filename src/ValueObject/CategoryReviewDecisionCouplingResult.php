<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CategoryReviewDecisionCouplingResultInterface;

/**
 * Represents the category review decision coupling result value.
 */
final readonly class CategoryReviewDecisionCouplingResult implements CategoryReviewDecisionCouplingResultInterface
{
    /**
     * @param list<string>       $blockers
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     */
    public function __construct(
        private string $requestId,
        private string $categoryId,
        private string $reviewState,
        private string $workflowState,
        private bool $publishable,
        private array $blockers,
        private array $warnings,
        private array $checks,
        private string $actorId,
        private string $reason,
    ) {
    }

    /**
     * Handles the request id workflow.
     */
    public function requestId(): string
    {
        return $this->requestId;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the review state workflow.
     */
    public function reviewState(): string
    {
        return $this->reviewState;
    }

    /**
     * Handles the workflow state workflow.
     */
    public function workflowState(): string
    {
        return $this->workflowState;
    }

    /**
     * Handles the publishable workflow.
     */
    public function publishable(): bool
    {
        return $this->publishable;
    }

    /**
     * Handles the blockers workflow.
     */
    public function blockers(): array
    {
        return $this->blockers;
    }

    /**
     * Handles the warnings workflow.
     */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /**
     * Handles the checks workflow.
     */
    public function checks(): array
    {
        return $this->checks;
    }

    /**
     * Handles the actor id workflow.
     */
    public function actorId(): string
    {
        return $this->actorId;
    }

    /**
     * Handles the reason workflow.
     */
    public function reason(): string
    {
        return $this->reason;
    }

    /**
     * Handles the payload workflow.
     */
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
