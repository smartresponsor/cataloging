<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Repository for Category — provides read/write with idempotency.
 */

namespace App\Service;

use SmartResponsor\Category\Layer\Domain\Category;

interface CatalogCategoryRepository
{
    public function save(Category $category): void;

    public function getById(string $id): ?Category;

    public function getBySlug(string $slug): ?Category;

    public function move(string $id, ?string $newParentId): void;
}
