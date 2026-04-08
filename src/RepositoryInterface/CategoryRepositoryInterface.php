<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\RepositoryInterface;
/**
 * Defines the contract for category repository.
 */
interface CategoryRepositoryInterface
{
    /** @return list<array<string,mixed>> */
    public function tree(string $taxonomyCode, ?string $parentId, int $depth, string $locale): array;

    /** @return list<array<string,mixed>> */
    public function breadcrumb(string $categoryId, string $locale): array;
    /**
     * Handles the slug exists workflow.
     */
    public function slugExists(string $slug, string $taxonomyId, ?string $parentId, string $locale): bool;

    /**
     * @param array<string,mixed> $name
     * @param array<string,mixed> $slug
     * @param array<string,mixed> $meta
     *
     * @return array<string,mixed>
     */
    public function create(string $taxonomyId, ?string $parentId, array $name, array $slug, array $meta): array;

    /** @return array<string,mixed> */
    public function move(string $actorId, string $categoryId, ?string $newParentId, int $newOrder): array;
    /**
     * Handles the attach workflow.
     */
    public function attach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void;
    /**
     * Handles the detach workflow.
     */
    public function detach(string $actorId, string $categoryId, string $targetDomain, string $targetClass, string $targetId): void;

    /** @return list<array<string,mixed>> */
    public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array;

    /** @return array<string,mixed> */
    public function bySlug(string $taxonomyCode, string $slug, string $locale): array;
    /**
     * Handles the full slug workflow.
     */
    public function fullSlug(string $categoryId, string $locale): string;
}
