<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryReviewQueueItemInterface;

/**
 * Represents the category review queue item value.
 */
final readonly class CategoryReviewQueueItem implements CategoryReviewQueueItemInterface
{
    /**
     * @param list<string> $readinessWarnings
     *
     * @noinspection PhpTooManyParametersInspection
     */
    public function __construct(
        private string $requestId,
        private string $categoryId,
        private string $assignedReviewer,
        private string $priority,
        private string $requestState,
        private bool $readyForReview,
        private array $readinessWarnings,
        private ?\DateTimeImmutable $dueAt,
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
     *
     * @return list<string>
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
