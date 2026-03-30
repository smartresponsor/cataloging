<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\RepositoryInterface\CategoryRepositoryInterface;

/**
 * SQL-backed repository. Actual SQL and connections are injected in infrastructure layer.
 * Here we keep contract-level shape and safe defaults.
 */
final class CategoryRepository implements CategoryRepositoryInterface
{
    /** @return list<array<string,mixed>> */
    public function tree(string $taxonomyCode, ?string $parentId, int $depth, string $locale): array
    {
        return [];
    }

    /** @return list<array<string,mixed>> */
    public function breadcrumb(string $categoryId, string $locale): array
    {
        return [];
    }

    public function slugExists(string $slug, string $taxonomyId, ?string $parentId, string $locale): bool
    {
        return false;
    }

    /**
     * @param array<string,mixed> $name
     * @param array<string,mixed> $slug
     * @param array<string,mixed> $meta
     *
     * @return array<string,mixed>
     */
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

    /** @return array<string,mixed> */
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

    /** @return list<array<string,mixed>> */
    public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array
    {
        return [];
    }

    /** @return array<string,mixed> */
    public function bySlug(string $taxonomyCode, string $slug, string $locale): array
    {
        return ['id' => '', 'taxonomyCode' => $taxonomyCode, 'slug' => $slug, 'locale' => $locale];
    }

    public function fullSlug(string $categoryId, string $locale): string
    {
        return '';
    }
}
