<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Service\Category\TreeOperation;
use PHPUnit\Framework\TestCase;

final class TreeOperationTest extends TestCase
{
    public function testMoveKeepsParent(): void
    {
        $op = new TreeOperation();
        $tree = [
            ['id' => 1, 'parent' => null],
            ['id' => 2, 'parent' => 1],
            ['id' => 3, 'parent' => 1],
        ];
        $result = $op->move($tree, 3, 2);
        self::assertSame(2, $result[2]['parent']);
    }
}
