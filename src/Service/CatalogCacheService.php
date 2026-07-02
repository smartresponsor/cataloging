<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

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

            if (!is_array($value)) {
                return [];
            }

            $tree = [];
            foreach ($value as $key => $entry) {
                if (!is_string($key)) {
                    continue;
                }

                $tree[$key] = $entry;
            }

            return $tree;
        }
        $tree = [];
        $item->set($tree);
        $this->pool->save($item);

        return $tree;
    }
}
