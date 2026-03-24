<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EntityInterface\CategoryAccessAssignmentInterface;

interface CatalogAccessAssignmentServiceInterface
{
    public function assignOwner(string $categoryId, string $actorUserId): CategoryAccessAssignmentInterface;

    public function assignRole(string $categoryId, string $actorUserId, string $role, bool $isPrimary = false): CategoryAccessAssignmentInterface;

    public function revoke(string $categoryId, string $actorUserId): void;

    public function setPrimary(string $categoryId, string $actorUserId): void;

    /** @return list<CategoryAccessAssignmentInterface> */
    public function listActiveForCategory(string $categoryId): array;
}
