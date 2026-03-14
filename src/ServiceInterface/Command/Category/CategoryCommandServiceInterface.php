<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Command\Category;

/**
 * Public contract for Category command service.
 * Methods return lightweight array views to keep UI/BFF decoupled from entities.
 */
interface CategoryCommandServiceInterface
{
    public function create(string $actorId, string $taxonomyId, ?string $parentId, array $name, array $slug, array $meta = []): array;

    public function move(string $actorId, string $categoryId, ?string $newParentId, int $newOrder): array;

    public function attach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void;

    public function detach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void;

    public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array;
}
