<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\CatalogCategory;

final class CategoryService
{
    private CategoryRepositoryInterface $repo;

    public function __construct(CategoryRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param array{parentId?: string, search?: string, first?: int, after?: string, withTotal?: bool, approxTotal?: bool} $opt
     *
     * @return array{edges: array<int, array{id: string, name: string, slug: string, level: int, path: string}>, pageInfo: array{endCursor?: string, hasNextPage: bool}, total?: int, approxTotal?: int}
     */
    public function list(array $opt = []): array
    {
        $withTotal = (bool) ($opt['withTotal'] ?? false);
        $approxTotal = (bool) ($opt['approxTotal'] ?? false);
        if ($withTotal && $approxTotal) {
            throw new \InvalidArgumentException('Choose either withTotal=true or approxTotal=true, not both.');
        }

        return $this->repo->list($opt, $withTotal, $approxTotal);
    }

    /**
     * @return array<int, array{id: string, name: string, slug: string}>
     */
    public function breadcrumb(string $id): array
    {
        return $this->repo->breadcrumb($id);
    }
}
