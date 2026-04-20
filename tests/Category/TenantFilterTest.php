<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Service\TenantFilter;
use PHPUnit\Framework\TestCase;

final class TenantFilterTest extends TestCase
{
    public function testFilterByTenant(): void
    {
        $f = new TenantFilter();
        $items = [
            ['id' => 1, 'tenant' => 'default'],
            ['id' => 2, 'tenant' => 'merchant'],
        ];
        $res = $f->filter($items, 'merchant');
        self::assertCount(1, $res);
        self::assertSame(2, $res[0]['id']);
    }
}
