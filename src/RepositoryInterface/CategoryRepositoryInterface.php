<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\RepositoryInterface;

use App\ValueObject\CategoryRepositoryCreateRequest;
use App\ValueObject\CategoryResolveRequest;
use App\ValueObject\CategoryServiceMoveRequest;
use App\ValueObject\CategorySlugExistsRequest;
use App\ValueObject\CategorySlugLookupRequest;
use App\ValueObject\CategoryTreeRequest;

/**
 * Defines the contract for category repository.
 */
interface CategoryRepositoryInterface
{
    /** @return list<array<string,mixed>> */
    public function tree(CategoryTreeRequest $request): array;

    /** @return list<array<string,mixed>> */
    public function breadcrumb(string $categoryId, string $locale): array;

    /**
     * Handles the slug exists workflow.
     */
    public function slugExists(CategorySlugExistsRequest $request): bool;

    /** @return array<string,mixed> */
    public function create(CategoryRepositoryCreateRequest $request): array;

    /** @return array<string,mixed> */
    public function move(CategoryServiceMoveRequest $request): array;

    /**
     * Handles the attach workflow.
     */
    public function attach(
        string $actorId,
        string $categoryId,
        string $targetDomain,
        string $targetClass,
        string $targetId,
    ): void;

    /**
     * Handles the detach workflow.
     */
    public function detach(
        string $actorId,
        string $categoryId,
        string $targetDomain,
        string $targetClass,
        string $targetId,
    ): void;

    /** @return list<array<string,mixed>> */
    public function resolve(CategoryResolveRequest $request): array;

    /** @return array<string,mixed> */
    public function bySlug(CategorySlugLookupRequest $request): array;

    /**
     * Handles the full slug workflow.
     */
    public function fullSlug(string $categoryId, string $locale): string;
}
