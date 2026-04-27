<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryAccessAssignmentEntityInterface;
use App\Cataloging\ValueObject\CatalogCategoryAccessAssignmentEntityRequest;
use App\Cataloging\ValueObject\CatalogCategoryAccessAssignmentEntitySelection;

/**
 * Defines the contract for catalog access assignment service.
 */
interface CatalogAccessAssignmentServiceInterface
{
    /**
     * Handles the assign owner workflow.
     */
    public function assignOwner(CatalogCategoryAccessAssignmentEntitySelection $selection): CatalogCategoryAccessAssignmentEntityInterface;

    /**
     * Handles the assign role workflow.
     */
    public function assignRole(CatalogCategoryAccessAssignmentEntityRequest $request): CatalogCategoryAccessAssignmentEntityInterface;

    /**
     * Handles the revoke workflow.
     */
    public function revoke(CatalogCategoryAccessAssignmentEntitySelection $selection): void;

    /**
     * Updates the primary value.
     */
    public function setPrimary(CatalogCategoryAccessAssignmentEntitySelection $selection): void;

    /** @return list<CatalogCategoryAccessAssignmentEntityInterface> */
    public function listActiveForCategory(string $categoryId): array;
}
