<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryChangeRequestReviewedInterface;

final class CategoryChangeRequestReviewed implements CategoryChangeRequestReviewedInterface
{
    public function __construct(
        private readonly string $requestId,
        private readonly string $categoryId,
        private readonly string $fromState,
        private readonly string $toState,
        private readonly string $reviewedBy,
        private readonly string $decisionReason,
        private readonly \DateTimeImmutable $reviewedAt,
    ) {
    }

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
