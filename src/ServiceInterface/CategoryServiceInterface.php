<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;
/**
 * Defines the contract for category service.
 */
interface CategoryServiceInterface
{
    /**
     * @param array<string,string>                                                              $name
     * @param array<string,string>                                                              $slug
     * @param array<string,array<string,bool|float|int|string|null>|bool|float|int|string|null> $meta
     *
     * @return array<string,mixed>
     */
    public function create(
        string $actorId,
        string $taxonomyId,
        ?string $parentId,
        array $name,
        array $slug,
        array $meta = [],
    ): array;

    /** @return array<string,mixed> */
    public function move(string $actorId, string $categoryId, ?string $newParentId, int $newOrder): array;
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
    public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array;
}
