<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryReviewQueueItemInterface;

final class CategoryReviewQueueItem implements CategoryReviewQueueItemInterface
{
    /** @param list<string> $readinessWarnings */
    public function __construct(
        private readonly string $requestId,
        private readonly string $categoryId,
        private readonly string $assignedReviewer,
        private readonly string $priority,
        private readonly string $requestState,
        private readonly bool $readyForReview,
        private readonly array $readinessWarnings,
        private readonly ?\DateTimeImmutable $dueAt,
    ) {
    }

    /** @param list<string> $readinessWarnings */
    public static function create(
        string $requestId,
        string $categoryId,
        string $assignedReviewer,
        string $priority,
        string $requestState,
        bool $readyForReview,
        array $readinessWarnings,
        ?\DateTimeImmutable $dueAt,
    ): self {
        return new self(
            $requestId,
            $categoryId,
            $assignedReviewer,
            $priority,
            $requestState,
            $readyForReview,
            $readinessWarnings,
            $dueAt,
        );
    }

    public function requestId(): string
    {
        return $this->requestId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function assignedReviewer(): string
    {
        return $this->assignedReviewer;
    }

    public function priority(): string
    {
        return $this->priority;
    }

    public function requestState(): string
    {
        return $this->requestState;
    }

    public function readyForReview(): bool
    {
        return $this->readyForReview;
    }

    public function readinessWarnings(): array
    {
        return $this->readinessWarnings;
    }

    public function dueAt(): ?\DateTimeImmutable
    {
        return $this->dueAt;
    }
}
