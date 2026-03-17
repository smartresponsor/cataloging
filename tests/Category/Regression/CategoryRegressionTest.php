<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Regression;

use PHPUnit\Framework\TestCase;

final class CategoryRegressionTest extends TestCase
{
    public function testCoreContractArtifactsStillExistAndReferenceRuntimeSurface(): void
    {
        $openApi = __DIR__.'/../../../api/category-openapi.yaml';
        $graphql = __DIR__.'/../../../config/graphql/category.yaml';
        $moveRoute = __DIR__.'/../../../config/routes/category-move.yaml';

        self::assertFileExists($openApi);
        self::assertFileExists($graphql);
        self::assertFileExists($moveRoute);
        self::assertFileExists(__DIR__.'/../../../src/Command/PublishCategoryCommand.php');
        self::assertFileExists(__DIR__.'/../../../src/Command/DumpCategoryTreeCommand.php');
        self::assertFileExists(__DIR__.'/../../../src/Command/CategoryRuntimeProbeCommand.php');
        self::assertFileExists(__DIR__.'/../../../src/Command/CategoryRuntimeClosureCommand.php');
        self::assertFileExists(__DIR__.'/../../../src/Command/CategoryRuntimeGateCommand.php');
        self::assertFileExists(__DIR__.'/../../../src/Command/CategoryRuntimeSelfCheckCommand.php');
        self::assertFileExists(__DIR__.'/../../../src/Command/CategoryRuntimeReleaseReportCommand.php');
        self::assertFileExists(__DIR__.'/../../../src/Command/CategoryRuntimeRcVerdictCommand.php');
        self::assertFileExists(__DIR__.'/../../../src/Command/CategoryRuntimeReleaseEnvelopeCommand.php');

        self::assertStringContainsString('/api/category/tree', (string) file_get_contents($openApi));
        self::assertStringContainsString('admin_category_move', (string) file_get_contents($moveRoute));
        self::assertStringContainsString('/admin/category/tree/move', (string) file_get_contents($moveRoute));
        self::assertStringContainsString('CategoryMoveController::__invoke', (string) file_get_contents($moveRoute));
        self::assertStringContainsString('category:runtime:gate', (string) file_get_contents(__DIR__.'/../../../src/Command/CategoryRuntimeGateCommand.php'));
        self::assertStringContainsString('category:runtime:self-check', (string) file_get_contents(__DIR__.'/../../../src/Command/CategoryRuntimeSelfCheckCommand.php'));
        self::assertStringContainsString('category:runtime:release-report', (string) file_get_contents(__DIR__.'/../../../src/Command/CategoryRuntimeReleaseReportCommand.php'));
        self::assertStringContainsString('category:runtime:rc-verdict', (string) file_get_contents(__DIR__.'/../../../src/Command/CategoryRuntimeRcVerdictCommand.php'));
        self::assertStringContainsString('category:runtime:release-envelope', (string) file_get_contents(__DIR__.'/../../../src/Command/CategoryRuntimeReleaseEnvelopeCommand.php'));
    }
}
