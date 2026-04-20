<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\Controller\Admin;

use App\Cataloging\Controller\Admin\CategoryRuntimeStatusController;
use App\Cataloging\ServiceInterface\Ops\CategoryRuntimeStatusViewBuilderInterface;
use PHPUnit\Framework\TestCase;

final class CategoryRuntimeStatusControllerSignatureTest extends TestCase
{
    public function testConstructorDependsOnInterface(): void
    {
        $reflection = new \ReflectionMethod(CategoryRuntimeStatusController::class, '__construct');
        $parameters = $reflection->getParameters();

        self::assertCount(1, $parameters);
        $type = $parameters[0]->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);
        self::assertSame(CategoryRuntimeStatusViewBuilderInterface::class, $type->getName());
    }
}
