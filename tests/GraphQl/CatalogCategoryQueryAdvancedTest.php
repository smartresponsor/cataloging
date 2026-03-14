<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\GraphQl;

use App\GraphQl\CategoryQuery;
use PHPUnit\Framework\TestCase;

final class CatalogCategoryQueryAdvancedTest extends TestCase
{
    public function testLocaleFilter(): void
    {
        $query = new CategoryQuery();
        $result = $query(null, ['locale' => 'en', 'first' => 5], null, null);

        self::assertNotEmpty($result);

        foreach ($result as $row) {
            self::assertSame('en', $row['locale']);
        }
    }
}
