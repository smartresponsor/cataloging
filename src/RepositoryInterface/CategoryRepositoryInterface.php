<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\RepositoryInterface;

interface CategoryRepositoryInterface
{
    public function tree(string $taxonomyCode, ?string $parentId, int $depth, string $locale): array;

    public function breadcrumb(string $categoryId, string $locale): array;

    public function slugExists(string $slug, string $taxonomyId, ?string $parentId, string $locale): bool;

    public function create(string $taxonomyId, ?string $parentId, array $name, array $slug, array $meta): array;

    public function move(string $actorId, string $categoryId, ?string $newParentId, int $newOrder): array;

    public function attach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void;

    public function detach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void;

    public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array;

    /** Find category by localized slug within taxonomy. */
    public function bySlug(string $taxonomyCode, string $slug, string $locale): array;

    /** Calculate/return localized full slug for a category. */
    public function fullSlug(string $categoryId, string $locale): string;
}
