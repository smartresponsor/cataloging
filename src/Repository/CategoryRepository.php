<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\RepositoryInterface\CategoryRepositoryInterface;
use App\ValueObject\CategoryResolveRequest;
use App\ValueObject\CategoryServiceMoveRequest;
use App\ValueObject\CategoryTreeRequest;

/**
 * SQL-backed repository. Actual SQL and connections are injected in infrastructure layer.
 * Here we keep contract-level shape and safe defaults.
 */
final class CategoryRepository implements CategoryRepositoryInterface
{
    /** @return list<array<string,mixed>> */
    public function tree(CategoryTreeRequest $request): array
    {
        return [];
    }

    /** @return list<array<string,mixed>> */
    public function breadcrumb(string $categoryId, string $locale): array
    {
        return [];
    }
    /**
     * Handles the slug exists workflow.
     */
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
    public function move(CategoryServiceMoveRequest $request): array
    {
        return ['id' => $request->categoryId(), 'parentId' => $request->newParentId(), 'order' => $request->newOrder()];
    }
    /**
     * Handles the attach workflow.
     */
    public function attach(
        string $actorId,
        string $categoryId,
        string $targetDomain,
        string $targetClass,
        string $targetId,
    ): void
    {
    }
    /**
     * Handles the detach workflow.
     */
    public function detach(
        string $actorId,
        string $categoryId,
        string $targetDomain,
        string $targetClass,
        string $targetId,
    ): void
    {
    }

    /** @return list<array<string,mixed>> */
    public function resolve(CategoryResolveRequest $request): array
    {
        return [];
    }

    /** @return array<string,mixed> */
    public function bySlug(string $taxonomyCode, string $slug, string $locale): array
    {
        return ['id' => '', 'taxonomyCode' => $taxonomyCode, 'slug' => $slug, 'locale' => $locale];
    }
    /**
     * Handles the full slug workflow.
     */
    public function fullSlug(string $categoryId, string $locale): string
    {
        return '';
    }
}
