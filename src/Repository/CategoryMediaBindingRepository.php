<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Repository;

use App\EntityInterface\CategoryMediaBindingInterface;
use App\EventInterface\CategoryMediaBoundInterface;
use App\RepositoryInterface\CategoryMediaBindingRepositoryInterface;

final class CategoryMediaBindingRepository implements CategoryMediaBindingRepositoryInterface
{
    /** @var array<string,CategoryMediaBindingInterface> */
    private array $bindings = [];

    /** @var list<CategoryMediaBoundInterface> */
    private array $history = [];

    public function save(CategoryMediaBindingInterface $binding): void
    {
        $this->bindings[$binding->bindingId()] = $binding;
    }

    public function find(string $bindingId): ?CategoryMediaBindingInterface
    {
        return $this->bindings[$bindingId] ?? null;
    }

    public function bindingsForCategory(string $categoryId): array
    {
        return array_values(array_filter(
            $this->bindings,
            static fn (CategoryMediaBindingInterface $binding): bool => $binding->categoryId() === $categoryId,
        ));
    }

    public function appendHistory(CategoryMediaBoundInterface $event): void
    {
        $this->history[] = $event;
    }

    public function history(): array
    {
        return $this->history;
    }
}
