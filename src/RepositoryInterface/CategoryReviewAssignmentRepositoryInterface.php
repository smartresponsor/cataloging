<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CategoryReviewAssignmentInterface;

/**
 * Defines the contract for category review assignment repository.
 */
interface CategoryReviewAssignmentRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CategoryReviewAssignmentInterface $assignment): void;

    /**
     * Handles the find by request id workflow.
     */
    public function findByRequestId(string $requestId): ?CategoryReviewAssignmentInterface;

    /** @return list<CategoryReviewAssignmentInterface> */
    public function findByReviewer(string $reviewer): array;

    /** @return list<CategoryReviewAssignmentInterface> */
    public function findByCategoryId(string $categoryId): array;
}
