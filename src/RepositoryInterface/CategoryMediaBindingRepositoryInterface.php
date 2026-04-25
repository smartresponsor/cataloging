<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CatalogCategoryMediaBindingEntityInterface;
use App\Cataloging\EventInterface\CategoryMediaBoundInterface;

/**
 * Defines the contract for category media binding repository.
 */
interface CatalogCategoryMediaBindingEntityRepositoryInterface
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
if (!class_exists(__NAMESPACE__.'\\CategoryMediaBindingRepositoryInterface', false)) {
    class_alias(CatalogCategoryMediaBindingEntityRepositoryInterface::class, __NAMESPACE__.'\\CategoryMediaBindingRepositoryInterface');
}
