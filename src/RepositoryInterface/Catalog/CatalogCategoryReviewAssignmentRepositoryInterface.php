<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface\Catalog;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryReviewAssignmentEntityInterface;

/**
 * Defines the contract for category review assignment repository.
 */
interface CatalogCategoryReviewAssignmentRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CatalogCategoryReviewAssignmentEntityInterface $assignment): void;

    /**
     * Handles the find by request id workflow.
     */
    public function findByRequestId(string $requestId): ?CatalogCategoryReviewAssignmentEntityInterface;

    /** @return list<CatalogCategoryReviewAssignmentEntityInterface> */
    public function findByReviewer(string $reviewer): array;

    /** @return list<CatalogCategoryReviewAssignmentEntityInterface> */
    public function findByCategoryId(string $categoryId): array;
}
