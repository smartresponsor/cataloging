<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EntityInterface\CategoryAccessAssignmentInterface;
/**
 * Defines the contract for catalog access assignment service.
 */
interface CatalogAccessAssignmentServiceInterface
{
    /**
     * Handles the assign owner workflow.
     */
    public function assignOwner(string $categoryId, string $actorUserId): CategoryAccessAssignmentInterface;
    /**
     * Handles the assign role workflow.
     */
    public function assignRole(
        string $categoryId,
        string $actorUserId,
        string $role,
        bool $isPrimary = false,
    ): CategoryAccessAssignmentInterface;
    /**
     * Handles the revoke workflow.
     */
    public function revoke(string $categoryId, string $actorUserId): void;
    /**
     * Updates the primary value.
     */
    public function setPrimary(string $categoryId, string $actorUserId): void;

    /** @return list<CategoryAccessAssignmentInterface> */
    public function listActiveForCategory(string $categoryId): array;
}
