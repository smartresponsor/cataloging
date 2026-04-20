<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event;

use App\Cataloging\EventInterface\CategoryReviewDecisionCoupledInterface;

/**
 * Represents the category review decision coupled application event.
 */
final readonly class CategoryReviewDecisionCoupled implements CategoryReviewDecisionCoupledInterface
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
        private \DateTimeImmutable $occurredAt,
    ) {
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
            'occurredAt' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
}
