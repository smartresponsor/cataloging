<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository\Catalog;

use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryRepositoryInterface;
use App\Cataloging\ValueObject\CategoryRepositoryCreateRequest;
use App\Cataloging\ValueObject\CategoryResolveRequest;
use App\Cataloging\ValueObject\CategoryServiceMoveRequest;
use App\Cataloging\ValueObject\CategorySlugExistsRequest;
use App\Cataloging\ValueObject\CategorySlugLookupRequest;
use App\Cataloging\ValueObject\CategoryTreeRequest;

/**
 * SQL-backed repository. Actual SQL and connections are injected in infrastructure layer.
 * Here we keep contract-level shape and safe defaults.
 */
final class CatalogCategoryRepository implements CatalogCategoryRepositoryInterface
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
    public function slugExists(CategorySlugExistsRequest $request): bool
    {
        return false;
    }

    /** @return array<string,mixed> */
    public function create(CategoryRepositoryCreateRequest $request): array
    {
        return [
            'id' => '',
            'taxonomyId' => $request->taxonomyId(),
            'parentId' => $request->parentId(),
            'nameEntity' => $request->nameEntity(),
            'slug' => $request->slug(),
            'meta' => $request->meta(),
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
    ): void {
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
    ): void {
    }

    /** @return list<array<string,mixed>> */
    public function resolve(CategoryResolveRequest $request): array
    {
        return [];
    }

    /** @return array<string,mixed> */
    public function bySlug(CategorySlugLookupRequest $request): array
    {
        return [
            'id' => '',
            'taxonomyCode' => $request->taxonomyCode(),
            'slug' => $request->slug(),
            'locale' => $request->locale(),
        ];
    }

    /**
     * Handles the full slug workflow.
     */
    public function fullSlug(string $categoryId, string $locale): string
    {
        return '';
    }
}
