<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * Provides the catalog cache service application service.
 */
final readonly class CatalogCacheService
{
    /**
     * Initializes the catalog cache service service collaborators.
     */
    public function __construct(private CacheItemPoolInterface $pool)
    {
    }

    /**
     * @param string $locale
     *
     * @return array<string,mixed>
     *
     * @throws InvalidArgumentException
     */
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
