<?php

declare(strict_types=1);

namespace App\Tests\Api;

use PHPUnit\Framework\TestCase;

final class CatalogTreeBrokenTest extends TestCase
{
    public function testDetectsBroken(): void
    {
        $data = [
            ['id' => 1, 'level' => 0],
            ['id' => 2, 'level' => -1],
        ];

        self::assertNotEmpty(array_filter($data, static fn (array $node): bool => $node['level'] < 0));
    }
}
