<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * GraphQL resolvers (framework-agnostic example).
 */

namespace SmartResponsor\Category\Api\Graphql;

use App\Service\Category\Domain\Category;
use App\Service\Category\Repository\CategoryRepository;

final class CategoryResolver
{
    public function __construct(private CategoryRepository $repo)
    {
    }

    public function node(string $id): ?Category
    {
        return $this->repo->getById($id);
    }

    public function category(string $slug): ?Category
    {
        return $this->repo->getBySlug($slug);
    }

    // tree, breadcrumbs, mutations would be wired similarly with batch loading in a real server.
}
