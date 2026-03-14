<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Workflow;

use PHPUnit\Framework\TestCase;

final class CatalogCategoryCreateMovePublishFlowTest extends TestCase
{
    public function testRuntimeBoundaryArtifactsExistForCreateMovePublishFlow(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/src/Controller/CatalogApiController.php');
        self::assertFileExists(dirname(__DIR__, 2).'/src/Controller/Admin/CategoryMoveController.php');
        self::assertFileExists(dirname(__DIR__, 2).'/src/Service/Workflow/Category/PublishOperation.php');
        self::assertFileExists(dirname(__DIR__, 2).'/config/routes/catalog-move.yaml');
    }
}
