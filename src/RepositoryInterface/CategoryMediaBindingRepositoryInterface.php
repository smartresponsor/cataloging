<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\RepositoryInterface;

use App\EntityInterface\CategoryMediaBindingInterface;
use App\EventInterface\CategoryMediaBoundInterface;

interface CategoryMediaBindingRepositoryInterface
{
    public function save(CategoryMediaBindingInterface $binding): void;

    public function find(string $bindingId): ?CategoryMediaBindingInterface;

    /** @return list<CategoryMediaBindingInterface> */
    public function bindingsForCategory(string $categoryId): array;

    public function appendHistory(CategoryMediaBoundInterface $event): void;

    /** @return list<CategoryMediaBoundInterface> */
    public function history(): array;
}
