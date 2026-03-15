<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Service\CategoryCacheService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class CategoryCacheServiceTest extends TestCase
{
    public function testGetTreeCaches(): void
    {
        $cache = new ArrayAdapter();
        $svc = new CategoryCacheService($cache);
        $first = $svc->getTree('en');
        $second = $svc->getTree('en');
        self::assertSame($first, $second);
    }
}
