<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CategoryReviewAssignmentInterface;

interface CategoryReviewAssignmentRepositoryInterface
{
    public function save(CategoryReviewAssignmentInterface $assignment): void;

    public function findByRequestId(string $requestId): ?CategoryReviewAssignmentInterface;

    /** @return list<CategoryReviewAssignmentInterface> */
    public function findByReviewer(string $reviewer): array;

    /** @return list<CategoryReviewAssignmentInterface> */
    public function findByCategoryId(string $categoryId): array;
}
