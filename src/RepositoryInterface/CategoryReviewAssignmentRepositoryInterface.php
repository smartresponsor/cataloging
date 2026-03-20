<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\RepositoryInterface;

use App\EntityInterface\CategoryReviewAssignmentInterface;

interface CategoryReviewAssignmentRepositoryInterface
{
    public function save(CategoryReviewAssignmentInterface $assignment): void;

    public function findByRequestId(string $requestId): ?CategoryReviewAssignmentInterface;

    /** @return list<CategoryReviewAssignmentInterface> */
    public function findByReviewer(string $reviewer): array;
}
