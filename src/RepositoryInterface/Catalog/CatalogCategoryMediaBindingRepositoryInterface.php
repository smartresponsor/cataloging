<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface\Catalog;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryMediaBindingEntityInterface;
use App\Cataloging\EventInterface\Catalog\CatalogCategoryMediaBoundEventInterface;

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
    public function appendHistory(CatalogCategoryMediaBoundEventInterface $event): void;

    /** @return list<CatalogCategoryMediaBoundEventInterface> */
    public function history(): array;
}
