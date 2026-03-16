<?php

declare(strict_types=1);

namespace App\Tests;

use App\Service\TenantFilter;
use PHPUnit\Framework\TestCase;

final class CatalogTenantFilterTest extends TestCase
{
    public function testFilterByTenant(): void
    {
        $filter = new TenantFilter();
        $items = [
            ['id' => 1, 'tenant' => 'default'],
            ['id' => 2, 'tenant' => 'merchant'],
        ];

        $result = $filter->filter($items, 'merchant');

        self::assertCount(1, $result);
        self::assertSame(2, $result[0]['id']);
    }
}
