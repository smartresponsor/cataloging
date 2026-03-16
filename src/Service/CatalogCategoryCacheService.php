<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;

final class CatalogCategoryCacheService
{
    public function __construct(private readonly CacheItemPoolInterface $pool)
    {
    }

    public function getTree(string $locale): array
    {
        $key = 'category_tree_'.$locale;
        $item = $this->pool->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        $tree = [];
        $item->set($tree);
        $this->pool->save($item);

        return $tree;
    }
}
