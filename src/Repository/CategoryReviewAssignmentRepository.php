<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\EntityInterface\CategoryReviewAssignmentInterface;
use App\Cataloging\RepositoryInterface\CategoryReviewAssignmentRepositoryInterface;

final class CategoryReviewAssignmentRepository implements CategoryReviewAssignmentRepositoryInterface
{
    /** @var array<string,CategoryReviewAssignmentInterface> */
    private array $assignments = [];

    public function save(CategoryReviewAssignmentInterface $assignment): void
    {
        $this->assignments[$assignment->requestId()] = $assignment;
    }

    public function findByRequestId(string $requestId): ?CategoryReviewAssignmentInterface
    {
        return $this->assignments[$requestId] ?? null;
    }

    public function findByReviewer(string $reviewer): array
    {
        return array_values(array_filter(
            $this->assignments,
            static fn (CategoryReviewAssignmentInterface $assignment): bool => $assignment->assignedReviewer() === $reviewer,
        ));
    }

    public function findByCategoryId(string $categoryId): array
    {
        return array_values(array_filter(
            $this->assignments,
            static fn (CategoryReviewAssignmentInterface $assignment): bool => $assignment->categoryId() === $categoryId,
        ));
    }
}
