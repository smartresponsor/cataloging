<?php

declare(strict_types=1);

namespace App\Tests;

use App\Service\TreeOperation;
use PHPUnit\Framework\TestCase;

final class CatalogCategoryTreeOperationTest extends TestCase
{
    public function testMoveRejectsSelfParenting(): void
    {
        $operation = new TreeOperation();

        $this->expectException(\InvalidArgumentException::class);
        $operation->move('3', '3');
    }
}
