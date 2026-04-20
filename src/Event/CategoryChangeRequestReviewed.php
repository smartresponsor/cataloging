<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event;

use App\Cataloging\EventInterface\CategoryChangeRequestReviewedInterface;

/**
 * Represents the category change request reviewed application event.
 */
final readonly class CategoryChangeRequestReviewed implements CategoryChangeRequestReviewedInterface
{
    /**
     * Initializes the category change request reviewed service collaborators.
     */
    public function __construct(
        private string $requestId,
        private string $categoryId,
        private string $fromState,
        private string $toState,
        private string $reviewedBy,
        private string $decisionReason,
        private \DateTimeImmutable $reviewedAt,
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
            'fromState' => $this->fromState,
            'toState' => $this->toState,
            'reviewedBy' => $this->reviewedBy,
            'decisionReason' => $this->decisionReason,
            'reviewedAt' => $this->reviewedAt->format(DATE_ATOM),
        ];
    }
}
