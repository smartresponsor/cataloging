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

        $op->move('3', '3');
    }
}
