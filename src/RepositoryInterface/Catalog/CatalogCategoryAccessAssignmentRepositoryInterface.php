<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface\Catalog;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryAccessAssignmentEntityInterface;

/**
 * Defines the contract for category access assignment repository.
 */
interface CatalogCategoryAccessAssignmentRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CatalogCategoryAccessAssignmentEntityInterface $assignment): void;

    /**
     * Handles the find primary for category id workflow.
     */
    public function findPrimaryForCategoryId(string $categoryId): ?CatalogCategoryAccessAssignmentEntityInterface;

    /** @return list<CatalogCategoryAccessAssignmentEntityInterface> */
    public function findActiveByCategoryId(string $categoryId): array;

    /** @return list<CatalogCategoryAccessAssignmentEntityInterface> */
    public function findActiveByActorUserId(string $actorUserId): array;

    /**
     * Handles the find one by category id and actor user id workflow.
     */
    public function findOneByCategoryIdAndActorUserId(
        string $categoryId,
        string $actorUserId,
    ): ?CatalogCategoryAccessAssignmentEntityInterface;
}
