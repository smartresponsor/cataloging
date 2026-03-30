<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategoryReviewAssignmentInterface;

final class CategoryReviewAssignment implements CategoryReviewAssignmentInterface
{
    public function __construct(
        private readonly string $requestId,
        private readonly string $categoryId,
        private readonly string $assignedReviewer,
        private readonly string $assignedBy,
        private readonly string $priority,
        private readonly \DateTimeImmutable $assignedAt,
        private readonly ?\DateTimeImmutable $dueAt,
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

    public function assignedBy(): string
    {
        return $this->assignedBy;
    }

    public function priority(): string
    {
        return $this->priority;
    }

    public function assignedAt(): \DateTimeImmutable
    {
        return $this->assignedAt;
    }

    public function dueAt(): ?\DateTimeImmutable
    {
        return $this->dueAt;
    }
}
