<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Tests\Regression;

use PHPUnit\Framework\TestCase;

final class CatalogOwnerOverlapReportToolTest extends TestCase
{
    public function testOwnerOverlapReportToolExists(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/tools/inspection/CatalogOwnerOverlapReport.php');
        self::assertFileExists(dirname(__DIR__, 2).'/tools/inspection/CatalogRouteInventoryReport.php');
        self::assertFileExists(dirname(__DIR__, 2).'/tools/inspection/CatalogClassAliasReport.php');
    }

    public function testComposerScriptsIncludeReports(): void
    {
        $content = file_get_contents(dirname(__DIR__, 2).'/composer.json');

        self::assertIsString($content);
        self::assertStringContainsString('report:owner-overlap', $content);
        self::assertStringContainsString('report:route-inventory', $content);
        self::assertStringContainsString('report:class-alias', $content);
    }
}
