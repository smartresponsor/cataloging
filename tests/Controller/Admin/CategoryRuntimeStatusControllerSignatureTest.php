<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\CategoryRuntimeStatusController;
use App\ServiceInterface\Ops\CategoryRuntimeStatusViewBuilderInterface;
use PHPUnit\Framework\TestCase;

final class CategoryRuntimeStatusControllerSignatureTest extends TestCase
{
    public function testConstructorDependsOnInterface(): void
    {
        $reflection = new \ReflectionMethod(CategoryRuntimeStatusController::class, '__construct');
        $parameters = $reflection->getParameters();

        self::assertCount(1, $parameters);
        self::assertSame(CategoryRuntimeStatusViewBuilderInterface::class, $parameters[0]->getType()?->getName());
    }
}
