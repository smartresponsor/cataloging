<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EntityInterface\CategoryAccessAssignmentInterface;
use App\Cataloging\ValueObject\CategoryAccessAssignmentRequest;
use App\Cataloging\ValueObject\CategoryAccessAssignmentSelection;

/**
 * Defines the contract for catalog access assignment service.
 */
interface CatalogAccessAssignmentServiceInterface
{
    /**
     * Handles the assign owner workflow.
     */
    public function assignOwner(CategoryAccessAssignmentSelection $selection): CategoryAccessAssignmentInterface;

    /**
     * Handles the assign role workflow.
     */
    public function assignRole(CategoryAccessAssignmentRequest $request): CategoryAccessAssignmentInterface;

    /**
     * Handles the revoke workflow.
     */
    public function revoke(CategoryAccessAssignmentSelection $selection): void;

    /**
     * Updates the primary value.
     */
    public function setPrimary(CategoryAccessAssignmentSelection $selection): void;

    /** @return list<CategoryAccessAssignmentInterface> */
    public function listActiveForCategory(string $categoryId): array;
}
