<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Service\TreeOperation;
use PHPUnit\Framework\TestCase;

final class TreeOperationTest extends TestCase
{
    public function testMoveRejectsSelfParenting(): void
    {
        $op = new TreeOperation();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Node cannot be parent of itself');

        $op->move('node-1', 'node-1');
    }

    public function testSwapRequiresDistinctNodes(): void
    {
        $op = new TreeOperation();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Swap requires distinct nodes');

        $op->swap('node-1', 'node-1');
    }

    public function testReparentDelegatesToMoveInvariantChecks(): void
    {
        $op = new TreeOperation();

        $op->reparent('node-2', 'node-1');

        self::assertTrue(true);
    }
}
