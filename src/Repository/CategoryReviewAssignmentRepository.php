<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\EntityInterface\CategoryReviewAssignmentInterface;
use App\Cataloging\RepositoryInterface\CategoryReviewAssignmentRepositoryInterface;

/**
 * Provides repository services for category review assignment repository.
 */
/** @noinspection PhpCSFixerValidationInspection */
final class CategoryReviewAssignmentRepository implements CategoryReviewAssignmentRepositoryInterface
{
    /** @var array<string,CategoryReviewAssignmentInterface> */
    private array $assignments = [];

    /**
     * Handles the save workflow.
     */
    public function save(CategoryReviewAssignmentInterface $assignment): void
    {
        $this->assignments[$assignment->requestId()] = $assignment;
    }

    /**
     * Handles the find by request id workflow.
     */
    public function findByRequestId(string $requestId): ?CategoryReviewAssignmentInterface
    {
        return $this->assignments[$requestId] ?? null;
    }

    /**
     * Handles the find by reviewer workflow.
     */
    public function findByReviewer(string $reviewer): array
    {
        return array_values(array_filter(
            $this->assignments,
            static fn (CategoryReviewAssignmentInterface $assignment): bool => $assignment->assignedReviewer() === $reviewer,
        ));
    }

    /**
     * Handles the find by category id workflow.
     */
    public function findByCategoryId(string $categoryId): array
    {
        return array_values(array_filter(
            $this->assignments,
            static fn (CategoryReviewAssignmentInterface $assignment): bool => $assignment->categoryId() === $categoryId,
        ));
    }
}
