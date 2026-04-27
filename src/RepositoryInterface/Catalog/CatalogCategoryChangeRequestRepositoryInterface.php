<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface\Catalog;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryChangeRequestEntityInterface;
use App\Cataloging\EventInterface\CategoryChangeRequestReviewedInterface;

/**
 * Defines the contract for category change request repository.
 */
interface CatalogCategoryChangeRequestRepositoryInterface
{
    /**
     * Handles the find by request id workflow.
     */
    public function findByRequestId(string $requestId): ?CatalogCategoryChangeRequestEntityInterface;

    /** @return list<CatalogCategoryChangeRequestEntityInterface> */
    public function findByCategoryId(string $categoryId): array;

    /**
     * Handles the save workflow.
     */
    public function save(CatalogCategoryChangeRequestEntityInterface $request): void;

    /**
     * Handles the append review history workflow.
     */
    public function appendReviewHistory(CategoryChangeRequestReviewedInterface $event): void;

    /** @return list<CategoryChangeRequestReviewedInterface> */
    public function reviewHistoryForRequestId(string $requestId): array;
}
