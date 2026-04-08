<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
/**
 * Provides the catalog cache service application service.
 */
final class CatalogCacheService
{
    /**
     * Initializes the catalog cache service service collaborators.
     */
    public function __construct(private readonly CacheItemPoolInterface $pool)
    {
    }

    /** @return array<string,mixed> */
    public function getTree(string $locale): array
    {
        $key = 'category_tree_'.$locale;
        $item = $this->pool->getItem($key);
        if ($item->isHit()) {
            $value = $item->get();

            return is_array($value) ? $value : [];
        }
        $tree = [];
        $item->set($tree);
        $this->pool->save($item);

        return $tree;
    }
}
