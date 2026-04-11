<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CategoryReadRepositoryListRequest;

/** LayerInterface mirror (canon) */
interface CategoryReadRepositoryInterface
{
    /**
     * @param array<string, mixed> $opt
     *
     * @return array{
 *     edges: array<int, array{id: string, name: string, slug: string, level: int, path: string}>,
 *     pageInfo: array{endCursor?: string, hasNextPage: bool},
 *     total?: int,
 *     approxTotal?: int,
 * }
     */
    public function list(CategoryReadRepositoryListRequest $request): array;

    /**
     * @return array<int, array{id: string, name: string, slug: string}>
     */
    public function breadcrumb(string $id): array;
}
