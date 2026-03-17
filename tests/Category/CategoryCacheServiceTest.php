<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Service\CatalogCategoryCacheService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class CategoryCacheServiceTest extends TestCase
{
    public function testGetTreeCaches(): void
    {
        $cache = new ArrayAdapter();
        $svc = new CatalogCategoryCacheService($cache);

        $first = $svc->getTree('en');
        $second = $svc->getTree('en');
        $third = $svc->getTree('es');

        self::assertSame($first, $second);
        self::assertSame([], $first);
        self::assertSame([], $third);
        self::assertCount(2, iterator_to_array($cache->getItems(['category_tree_en', 'category_tree_es'])));
    }
}
