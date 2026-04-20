<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\EntityInterface\CategoryMediaBindingInterface;
use App\Cataloging\EventInterface\CategoryMediaBoundInterface;
use App\Cataloging\RepositoryInterface\CategoryMediaBindingRepositoryInterface;

/**
 * Provides repository services for category media binding repository.
 */
final class CategoryMediaBindingRepository implements CategoryMediaBindingRepositoryInterface
{
    /** @var array<string,CategoryMediaBindingInterface> */
    private array $bindings = [];

    /** @var list<CategoryMediaBoundInterface> */
    private array $history = [];

    /**
     * Handles the save workflow.
     */
    public function save(CategoryMediaBindingInterface $binding): void
    {
        $this->bindings[$binding->bindingId()] = $binding;
    }

    /**
     * Finds the requested record in the underlying store.
     */
    public function find(string $bindingId): ?CategoryMediaBindingInterface
    {
        return $this->bindings[$bindingId] ?? null;
    }

    /**
     * Handles the bindings for category workflow.
     */
    public function bindingsForCategory(string $categoryId): array
    {
        return array_values(array_filter(
            $this->bindings,
            static fn (CategoryMediaBindingInterface $binding): bool => $binding->categoryId() === $categoryId,
        ));
    }

    /**
     * Handles the append history workflow.
     */
    public function appendHistory(CategoryMediaBoundInterface $event): void
    {
        $this->history[] = $event;
    }

    /**
     * Handles the history workflow.
     */
    public function history(): array
    {
        return $this->history;
    }
}
