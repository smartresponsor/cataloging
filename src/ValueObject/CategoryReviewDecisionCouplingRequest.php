<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category review decision coupling workflows.
 */
final readonly class CategoryReviewDecisionCouplingRequest
{
    /** @param array<string,bool> $checks */
    public function __construct(
        private string $requestId,
        private string $targetState,
        private string $reviewedBy,
        private string $decisionReason,
        private array $checks = [],
    ) {
    }

    public function requestId(): string
    {
        return $this->requestId;
    }

    public function targetState(): string
    {
        return $this->targetState;
    }

    public function reviewedBy(): string
    {
        return $this->reviewedBy;
    }

    public function decisionReason(): string
    {
        return $this->decisionReason;
    }

    /** @return array<string,bool> */
    public function checks(): array
    {
        return $this->checks;
    }
}
