<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

/**
 * Public contract for tests service.
 * Methods return lightweight array views to keep UI/BFF decoupled from entities.
 */
interface CatalogtestsInterface
{
    /** Create category under parent within taxonomy. */
    public function create(string $taxonomyId, ?string $parentId, array $name, array $slug, array $meta = []): array;

    /** Move category to a new parent with explicit order. */
    public function move(string $categoryId, ?string $newParentId, int $newOrder): array;

    /** Attach an existing target entity to category. */
    public function attach(string $categoryId, string $targetDomain, string $targetClass, string $targetId): void;

    /** Detach target entity from category. */
    public function detach(string $categoryId, string $targetDomain, string $targetClass, string $targetId): void;

    /** Resolve categories for a target entity in taxonomy. */
    public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array;
}
