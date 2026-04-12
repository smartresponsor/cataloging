<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategoryReviewAssignmentInterface;

/**
 * Represents the category review assignment domain record.
 */
/** @noinspection PhpTooManyParametersInspection */
final readonly class CategoryReviewAssignment implements CategoryReviewAssignmentInterface
{
    /**
     * Initializes the category review assignment service collaborators.
     */
    /** @noinspection PhpTooManyParametersInspection */
    public function __construct(
        private string $requestId,
        private string $categoryId,
        private string $assignedReviewer,
        private string $assignedBy,
        private string $priority,
        private \DateTimeImmutable $assignedAt,
        private ?\DateTimeImmutable $dueAt,
    ) {
    }

    public static function create(
        string $requestId,
        string $categoryId,
        string $assignedReviewer,
        string $assignedBy,
        string $priority,
        ?\DateTimeImmutable $dueAt,
    ): self {
        return new self(
            trim($requestId),
            trim($categoryId),
            trim($assignedReviewer),
            trim($assignedBy),
            trim($priority),
            new \DateTimeImmutable('now'),
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
     * Handles the assigned by workflow.
     */
    public function assignedBy(): string
    {
        return $this->assignedBy;
    }

    /**
     * Handles the priority workflow.
     */
    public function priority(): string
    {
        return $this->priority;
    }

    /**
     * Handles the assigned at workflow.
     */
    public function assignedAt(): \DateTimeImmutable
    {
        return $this->assignedAt;
    }

    /**
     * Handles the due at workflow.
     */
    public function dueAt(): ?\DateTimeImmutable
    {
        return $this->dueAt;
    }
}
