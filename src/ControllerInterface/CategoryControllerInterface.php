<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ControllerInterface;

/**
 * Defines the contract for category controller.
 */
interface CategoryControllerInterface
{
    /**
     * GET /category/{taxonomy}/tree?locale=en&depth=2&parentId=ULID.
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed> $route
     *
     * @return list<array<string, mixed>>
     */
    public function tree(array $query, array $route): array;

    /**
     * GET /category/{taxonomy}/by-slug/{slug}?locale=en.
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed> $route
     *
     * @return array{category: array<string, mixed>, breadcrumb: mixed, seo: mixed}
     */
    public function bySlug(array $query, array $route): array;

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     *
     * @return array<string, mixed>
     */
    public function create(array $body, array $route, array $auth): array;

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     *
     * @return array<string, mixed>
     */
    public function move(array $body, array $route, array $auth): array;

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     */
    public function attach(array $body, array $route, array $auth): void;

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $route
     * @param array<string, mixed> $auth
     */
    public function detach(array $body, array $route, array $auth): void;
}
