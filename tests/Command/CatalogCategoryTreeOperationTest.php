<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Command;

use App\Service\Command\Category\TreeOperation;
use PHPUnit\Framework\TestCase;

final class CatalogCategoryTreeOperationTest extends TestCase
{
    public function testMoveAcceptsDistinctParent(): void
    {
        $operation = new TreeOperation();

        self::assertNull($operation->move('3', '2'));
    }

    public function testMoveRejectsSelfParent(): void
    {
        $operation = new TreeOperation();

        $this->expectException(\InvalidArgumentException::class);
        $operation->move('3', '3');
    }
}
