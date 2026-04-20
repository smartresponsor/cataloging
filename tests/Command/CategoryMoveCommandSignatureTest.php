<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\Command;

use App\Cataloging\Command\CategoryMoveCommand;
use App\Cataloging\ServiceInterface\CategoryMoveInterface;
use PHPUnit\Framework\TestCase;

final class CategoryMoveCommandSignatureTest extends TestCase
{
    public function testConstructorDependsOnInterface(): void
    {
        $reflection = new \ReflectionMethod(CategoryMoveCommand::class, '__construct');
        $parameters = $reflection->getParameters();

        self::assertCount(1, $parameters);
        $type = $parameters[0]->getType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);
        self::assertSame(CategoryMoveInterface::class, $type->getName());
    }
}
