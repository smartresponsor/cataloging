<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Tests\Parity;

use PHPUnit\Framework\TestCase;

final class CatalogFixtureDemoParityTest extends TestCase
{
    public function testParityYamlFixtureContainsWeakEntities(): void
    {
        $content = file_get_contents(dirname(__DIR__, 2).'/fixtures/Category/parity.yaml');

        self::assertIsString($content);
        self::assertStringContainsString('category_taxonomy', $content);
        self::assertStringContainsString('category_link', $content);
        self::assertStringContainsString('category_redirect', $content);
        self::assertStringContainsString('projection_control', $content);
        self::assertStringContainsString('virtual_category', $content);
    }

    public function testParityDemoJsonContainsWeakEntities(): void
    {
        $content = file_get_contents(dirname(__DIR__, 2).'/public/demo/category-parity.json');

        self::assertIsString($content);
        self::assertStringContainsString('category_taxonomy', $content);
        self::assertStringContainsString('category_link', $content);
        self::assertStringContainsString('category_redirect', $content);
        self::assertStringContainsString('projection_control', $content);
        self::assertStringContainsString('virtual_category', $content);
    }
}
