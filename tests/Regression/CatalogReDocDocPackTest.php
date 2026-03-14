<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Tests\Regression;

use PHPUnit\Framework\TestCase;

final class CatalogReDocDocPackTest extends TestCase
{
    public function testReDocAssetsExist(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/public/doc/redoc/index.html');
        self::assertFileExists(dirname(__DIR__, 2).'/public/doc/redoc/catalog-openapi.yaml');
    }

    public function testReDocRoutesExist(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/config/routes/catalog-redoc.yaml');
        $content = file_get_contents(dirname(__DIR__, 2).'/config/routes/catalog-redoc.yaml');

        self::assertIsString($content);
        self::assertStringContainsString('/api/redoc', $content);
        self::assertStringContainsString('/api/redoc/openapi.yaml', $content);
    }
}
