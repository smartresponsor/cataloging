<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CategoryGraphqlMoveRequest;
use App\ValueObject\CategoryGraphqlNodeRequest;
use App\ValueObject\CategoryGraphqlPublishRequest;

/**
 * Defines the contract for graphql resolver.
 */
interface GraphqlResolverInterface
{
    /** @return array<string,mixed>|null */
    public function category(CategoryGraphqlNodeRequest $request): ?array;

    /** @return list<array<string,mixed>> */
    public function categoryPath(CategoryGraphqlNodeRequest $request): array;

    /** @return array<string,mixed>|null */
    public function publishCategory(CategoryGraphqlPublishRequest $request): ?array;

    public function moveCategory(CategoryGraphqlMoveRequest $request): bool;
}
