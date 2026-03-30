<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\EntityInterface\CategoryAccessAssignmentInterface;
use App\RepositoryInterface\CategoryAccessAssignmentRepositoryInterface;

final class CategoryAccessAssignmentRepository implements CategoryAccessAssignmentRepositoryInterface
{
    /** @var array<string,CategoryAccessAssignmentInterface> */
    private array $assignments = [];

    public function save(CategoryAccessAssignmentInterface $assignment): void
    {
        $this->assignments[$assignment->assignmentId()] = $assignment;
    }

    public function findPrimaryForCategoryId(string $categoryId): ?CategoryAccessAssignmentInterface
    {
        foreach ($this->findActiveByCategoryId($categoryId) as $assignment) {
            if ($assignment->isPrimary()) {
                return $assignment;
            }
        }

        return null;
    }

    public function findActiveByCategoryId(string $categoryId): array
    {
        return array_values(array_filter(
            $this->assignments,
            static fn (CategoryAccessAssignmentInterface $assignment): bool => $assignment->categoryId() === $categoryId && 'active' === $assignment->status(),
        ));
    }

    public function findActiveByActorUserId(string $actorUserId): array
    {
        return array_values(array_filter(
            $this->assignments,
            static fn (CategoryAccessAssignmentInterface $assignment): bool => $assignment->actorUserId() === $actorUserId && 'active' === $assignment->status(),
        ));
    }

    public function findOneByCategoryIdAndActorUserId(string $categoryId, string $actorUserId): ?CategoryAccessAssignmentInterface
    {
        foreach ($this->assignments as $assignment) {
            if ($assignment->categoryId() === $categoryId && $assignment->actorUserId() === $actorUserId) {
                return $assignment;
            }
        }

        return null;
    }
}
