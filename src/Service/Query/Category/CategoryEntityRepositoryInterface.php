<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Query\Category;

use App\Entity\Category;

interface CategoryEntityRepositoryInterface
{
    public function save(Category $category): void;

    public function getById(string $id): ?Category;

    public function getBySlug(string $slug): ?Category;

    public function move(string $id, ?string $newParentId): void;
}
