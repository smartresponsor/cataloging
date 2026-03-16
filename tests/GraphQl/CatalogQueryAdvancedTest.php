<?php

declare(strict_types=1);

namespace App\Tests\GraphQl;

use App\GraphQL\testsQuery;
use GraphQL\Type\Definition\ResolveInfo;
use PHPUnit\Framework\TestCase;

final class CatalogQueryAdvancedTest extends TestCase
{
    public function testLocaleFilter(): void
    {
        $query = new testsQuery();
        $info = $this->createMock(ResolveInfo::class);

        $result = $query(null, ['locale' => 'en', 'first' => 5], null, $info);

        self::assertNotEmpty($result);
        foreach ($result as $row) {
            self::assertSame('en', $row['locale']);
        }
    }
}
