<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Api;

use PHPUnit\Framework\TestCase;

final class CategoryApiTest extends TestCase
{
    public function testOpenApiContainsCoreCategoryMutationAndReadPaths(): void
    {
        $path = dirname(__DIR__, 3).'/api/category-openapi.yaml';
        self::assertFileExists($path);

        $contents = file_get_contents($path);
        self::assertIsString($contents);
        self::assertStringContainsString('/api/category/tree:', $contents);
        self::assertStringContainsString('/api/category/{id}/move:', $contents);
        self::assertStringContainsString('/api/category/{id}/publish:', $contents);
        self::assertStringContainsString('version: 1.0.0-rc1', $contents);
    }
}
