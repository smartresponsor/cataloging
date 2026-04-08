<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\RepositoryInterface;

use App\EntityInterface\CategoryAccessAssignmentInterface;
/**
 * Defines the contract for category access assignment repository.
 */
interface CategoryAccessAssignmentRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CategoryAccessAssignmentInterface $assignment): void;
    /**
     * Handles the find primary for category id workflow.
     */
    public function findPrimaryForCategoryId(string $categoryId): ?CategoryAccessAssignmentInterface;

    /** @return list<CategoryAccessAssignmentInterface> */
    public function findActiveByCategoryId(string $categoryId): array;

    /** @return list<CategoryAccessAssignmentInterface> */
    public function findActiveByActorUserId(string $actorUserId): array;
    /**
     * Handles the find one by category id and actor user id workflow.
     */
    public function findOneByCategoryIdAndActorUserId(string $categoryId, string $actorUserId): ?CategoryAccessAssignmentInterface;
}
