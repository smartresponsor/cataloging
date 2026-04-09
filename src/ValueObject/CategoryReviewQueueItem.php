<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryReviewQueueItemInterface;
/**
 * Represents the category review queue item value.
 */
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
     * Handles the assigned reviewer workflow.
     */
    public function assignedReviewer(): string
    {
        return $this->assignedReviewer;
    }
    /**
     * Handles the priority workflow.
     */
    public function priority(): string
    {
        return $this->priority;
    }
    /**
     * Handles the request state workflow.
     */
    public function requestState(): string
    {
        return $this->requestState;
    }
    /**
     * Handles the ready for review workflow.
     */
    public function readyForReview(): bool
    {
        return $this->readyForReview;
    }
    /**
     * Handles the readiness warnings workflow.
     */
    public function readinessWarnings(): array
    {
        return $this->readinessWarnings;
    }
    /**
     * Handles the due at workflow.
     */
    public function dueAt(): ?\DateTimeImmutable
    {
        return $this->dueAt;
    }
}
