<?php

declare(strict_types=1);

namespace App\Tests;

use App\Service\CatalogCategoryCacheService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class CatalogCategoryCacheServiceTest extends TestCase
{
    public function testGetTreeCaches(): void
    {
        $cache = new ArrayAdapter();
        $service = new CatalogCategoryCacheService($cache);

        $first = $service->getTree('en');
        $second = $service->getTree('en');

        self::assertSame($first, $second);
    }
}
