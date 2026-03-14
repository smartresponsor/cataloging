<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Api;

use PHPUnit\Framework\TestCase;

final class CatalogApiTest extends TestCase
{
    public function testCategoryApiControllerRoutesAreDeclared(): void
    {
        $content = file_get_contents(dirname(__DIR__, 2).'/src/Controller/CatalogApiController.php');

        self::assertIsString($content);
        self::assertStringContainsString('/api/catalog/tree', $content);
        self::assertStringContainsString('/api/catalog/{id}/move', $content);
        self::assertStringContainsString('/api/catalog/{id}/publish', $content);
    }

    public function testMoveRouteConfigPointsToCanonicalController(): void
    {
        $content = file_get_contents(dirname(__DIR__, 2).'/config/routes/catalog-move.yaml');

        self::assertIsString($content);
        self::assertStringContainsString('App\Controller\Admin\CategoryMoveController::move', $content);
    }
}
