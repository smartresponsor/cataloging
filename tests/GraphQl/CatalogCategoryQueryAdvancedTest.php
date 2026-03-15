<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\GraphQl;

use App\GraphQL\Category\CategoryQuery;
use PHPUnit\Framework\TestCase;

final class CatalogCategoryQueryAdvancedTest extends TestCase
{
    public function testLocaleFilter(): void
    {
        $q = new CategoryQuery();
        $res = $q(null, ['locale' => 'en', 'first' => 5], null, null);
        $this->assertNotEmpty($res);
        foreach ($res as $row) {
            $this->assertSame('en', $row['locale']);
        }
    }
}
