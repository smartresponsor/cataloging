<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CategoryMediaBindingInterface;
use App\Cataloging\EventInterface\CategoryMediaBoundInterface;

/**
 * Defines the contract for category media binding repository.
 */
interface CategoryMediaBindingRepositoryInterface
{
    /**
     * Handles the save workflow.
     */
    public function save(CategoryMediaBindingInterface $binding): void;

    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $bindingId): ?CategoryMediaBindingInterface;

    /** @return list<CategoryMediaBindingInterface> */
    public function bindingsForCategory(string $categoryId): array;

    /**
     * Handles the append history workflow.
     */
    public function appendHistory(CategoryMediaBoundInterface $event): void;

    /** @return list<CategoryMediaBoundInterface> */
    public function history(): array;
}
