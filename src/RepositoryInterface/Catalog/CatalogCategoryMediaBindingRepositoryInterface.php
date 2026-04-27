<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface\Catalog;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryMediaBindingEntityInterface;
use App\Cataloging\EventInterface\CategoryMediaBoundInterface;

/**
 * Defines the contract for category media binding repository.
 */
interface CatalogCategoryMediaBindingRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CatalogCategoryMediaBindingEntityInterface $binding): void;

    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $bindingId): ?CatalogCategoryMediaBindingEntityInterface;

    /** @return list<CatalogCategoryMediaBindingEntityInterface> */
    public function bindingsForCategory(string $categoryId): array;

    /**
     * Handles the append history workflow.
     */
    public function appendHistory(CategoryMediaBoundInterface $event): void;

    /** @return list<CategoryMediaBoundInterface> */
    public function history(): array;
}
