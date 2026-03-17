<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Api;

use App\Service\CatalogCategory\DraftPolicy;
use App\Service\CatalogCategory\PublishOperation;
use App\Service\CatalogCategory\TreeOperation;
use App\ServiceInterface\Api\GraphqlResolverInterface;

/**
 * API namespace compatibility wrapper. Canonical behavior lives in App\Service\GraphqlResolver.
 */
final class GraphqlResolver implements GraphqlResolverInterface
{
    private \App\Service\GraphqlResolver $inner;

    public function __construct(?PublishOperation $publish = null, ?TreeOperation $tree = null)
    {
        $publish ??= new PublishOperation(new DraftPolicy());
        $tree ??= new TreeOperation();
        $this->inner = new \App\Service\GraphqlResolver($publish, $tree);
    }

    public function category(array $args): ?array
    {
        return $this->inner->category($args);
    }

    public function categoryPath(array $args): array
    {
        return $this->inner->categoryPath($args);
    }

    public function publishCategory(array $args): ?array
    {
        return $this->inner->publishCategory($args);
    }

    public function moveCategory(array $args): bool
    {
        return $this->inner->moveCategory($args);
    }
}
