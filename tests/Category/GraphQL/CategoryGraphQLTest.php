<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\GraphQL;

use PHPUnit\Framework\TestCase;

final class CategoryGraphQLTest extends TestCase
{
    public function testSchemaPresent(): void
    {
        $path = __DIR__.'/../../../config/graphql/category.yaml';

        $this->assertFileExists($path);
        $this->assertStringContainsString('Category', (string) file_get_contents($path));
    }
}
