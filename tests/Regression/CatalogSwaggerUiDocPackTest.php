<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Tests\Regression;

use PHPUnit\Framework\TestCase;

final class CatalogSwaggerUiDocPackTest extends TestCase
{
    public function testSwaggerUiAssetsExist(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/public/doc/swagger/index.html');
        self::assertFileExists(dirname(__DIR__, 2).'/public/doc/swagger/swagger-initializer.js');
        self::assertFileExists(dirname(__DIR__, 2).'/public/doc/swagger/catalog-openapi.yaml');
    }

    public function testSwaggerRoutesExist(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/config/routes/catalog-doc.yaml');
        $content = file_get_contents(dirname(__DIR__, 2).'/config/routes/catalog-doc.yaml');

        self::assertIsString($content);
        self::assertStringContainsString('/api/doc', $content);
        self::assertStringContainsString('/api/doc/openapi.yaml', $content);
    }
}
