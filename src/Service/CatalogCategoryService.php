<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Service layer — transactional operations for Category.
 */

namespace App\Service;

use App\Service\CatalogCategory\Domain\Category;
use App\Service\CatalogCategory\Repository\CategoryRepository;

final class CatalogCategoryService
{
    public function __construct(private CategoryRepository $repo)
    {
    }

    public function create(Category $category): Category
    {
        $this->repo->save($category);

        return $category;
    }

    public function update(Category $category): Category
    {
        $this->repo->save($category);

        return $category;
    }

    public function move(string $id, ?string $newParentId): Category
    {
        $this->repo->move($id, $newParentId);
        $updated = $this->repo->getById($id);
        if (!$updated) {
            throw new \RuntimeException('Category not found after move');
        }

        return $updated;
    }
}
