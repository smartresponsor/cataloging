<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\RepositoryInterface;

use App\EntityInterface\CategoryAccessAssignmentInterface;

interface CategoryAccessAssignmentRepositoryInterface
{
    public function save(CategoryAccessAssignmentInterface $assignment): void;

    public function findPrimaryForCategoryId(string $categoryId): ?CategoryAccessAssignmentInterface;

    /** @return list<CategoryAccessAssignmentInterface> */
    public function findActiveByCategoryId(string $categoryId): array;

    /** @return list<CategoryAccessAssignmentInterface> */
    public function findActiveByActorUserId(string $actorUserId): array;

    public function findOneByCategoryIdAndActorUserId(string $categoryId, string $actorUserId): ?CategoryAccessAssignmentInterface;
}
