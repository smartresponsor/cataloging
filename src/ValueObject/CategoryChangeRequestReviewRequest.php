<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries the full input surface for category change request review workflows.
 */
final readonly class CategoryChangeRequestReviewRequest
{
    public function __construct(
        private string $requestId,
        private string $targetState,
        private string $reviewedBy,
        private string $decisionReason,
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
}
