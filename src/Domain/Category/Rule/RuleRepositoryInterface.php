<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\CatalogCategory\Domain\Rule;

interface RuleRepositoryInterface
{
    /**
     * @param array{id?: string, name: string, definition: array<string, mixed>} $rule
     *
     * @return string id
     */
    public function save(array $rule): string;

    /**
     * @return array{id: string, name: string, definition: array<string, mixed>}|null
     */
    public function find(string $id): ?array;

    /**
     * @param array{search?: string, first?: int, after?: string} $opt
     *
     * @return array{edges: array<int, array{id: string, name: string}>, pageInfo: array{endCursor?: string, hasNextPage: bool}}
     */
    public function list(array $opt = []): array;
}
