<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\GraphQl;

use PHPUnit\Framework\TestCase;

final class CatalogGraphQlSurfaceTest extends TestCase
{
    public function testGraphqlServiceConfigRegistersResolvers(): void
    {
        $content = file_get_contents(dirname(__DIR__, 2).'/config/services_graphql.yaml');

        self::assertIsString($content);
        self::assertStringContainsString('App\GraphQl\CategoryChildListResolver', $content);
        self::assertStringContainsString('App\GraphQl\CategoryAncestorListResolver', $content);
    }

    public function testGraphqlSchemaConfigExists(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/config/graphql/catalog.yaml');
    }
}
