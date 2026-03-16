<?php

declare(strict_types=1);

namespace App\Tests\GraphQl;

use App\GraphQl\CategoryQuery;
use PHPUnit\Framework\TestCase;

final class CatalogQueryAdvancedTest extends TestCase
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
