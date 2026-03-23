<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryReviewDecisionCoupledInterface;

final class CategoryReviewDecisionCoupled implements CategoryReviewDecisionCoupledInterface
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
        private readonly \DateTimeImmutable $occurredAt,
    ) {
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
            'occurredAt' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
}
