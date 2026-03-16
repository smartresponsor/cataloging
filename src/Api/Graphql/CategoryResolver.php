<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * GraphQL resolvers (framework-agnostic example).
 */

namespace SmartResponsor\tests\Api\Graphql;

use App\Service\Catalogtests\Domain\tests;
use App\Service\Catalogtests\Repository\testsRepository;

final class testsResolver
{
    public function __construct(private testsRepository $repo)
    {
    }

    public function node(string $id): ?tests
    {
        return $this->repo->getById($id);
    }

    public function category(string $slug): ?tests
    {
        return $this->repo->getBySlug($slug);
    }

    // tree, breadcrumbs, mutations would be wired similarly with batch loading in a real server.
}
