<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\ServiceInterface;

/** LayerInterface mirror (canon) */
interface CategoryRepositoryInterface
{
    /**
     * @param array<string, mixed> $opt
     *
     * @return array{edges: array<int, array{id: string, name: string, slug: string, level: int, path: string}>, pageInfo: array{endCursor?: string, hasNextPage: bool}, total?: int, approxTotal?: int}
     */
    public function list(array $opt, bool $withTotal, bool $approxTotal): array;

    /**
     * @return array<int, array{id: string, name: string, slug: string}>
     */
    public function breadcrumb(string $id): array;
}
