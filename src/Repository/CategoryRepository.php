<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\RepositoryInterface\CategoryRepositoryInterface;

/**
 * SQL-backed repository. Actual SQL and connections are injected in infrastructure layer.
 * Here we keep contract-level shape and safe defaults.
 */
final class CategoryRepository implements CategoryRepositoryInterface
{
    public function tree(string $taxonomyCode, ?string $parentId, int $depth, string $locale): array
    {
        return [];
    }

    public function breadcrumb(string $categoryId, string $locale): array
    {
        return [];
    }

    public function slugExists(string $slug, string $taxonomyId, ?string $parentId, string $locale): bool
    {
        return false;
    }

    public function create(string $taxonomyId, ?string $parentId, array $name, array $slug, array $meta): array
    {
        return [
            'id' => '',
            'taxonomyId' => $taxonomyId,
            'parentId' => $parentId,
            'name' => $name,
            'slug' => $slug,
            'meta' => $meta,
            'path' => '',
            'order' => 0,
        ];
    }

    public function move(string $actorId, string $categoryId, ?string $newParentId, int $newOrder): array
    {
        return ['id' => $categoryId, 'parentId' => $newParentId, 'order' => $newOrder];
    }

    public function attach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
    {
    }

    public function detach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
    {
    }

    public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array
    {
        return [];
    }
}
