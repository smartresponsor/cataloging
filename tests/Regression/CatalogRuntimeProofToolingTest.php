<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Tests\Regression;

use PHPUnit\Framework\TestCase;

final class CatalogRuntimeProofToolingTest extends TestCase
{
    public function testRuntimeProofSmokeScriptsExist(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/tools/smoke/CatalogContainerBootSmoke.php');
        self::assertFileExists(dirname(__DIR__, 2).'/tools/smoke/CatalogDoctrineMappingSmoke.php');
        self::assertFileExists(dirname(__DIR__, 2).'/tools/smoke/CatalogFixtureLoadSmoke.php');
        self::assertFileExists(dirname(__DIR__, 2).'/tools/smoke/CatalogGraphqlSmoke.php');
    }

    public function testComposerScriptsExposeRuntimeProofCommands(): void
    {
        $content = file_get_contents(dirname(__DIR__, 2).'/composer.json');

        self::assertIsString($content);
        self::assertStringContainsString('smoke:container', $content);
        self::assertStringContainsString('smoke:doctrine', $content);
        self::assertStringContainsString('smoke:fixture-load', $content);
        self::assertStringContainsString('smoke:graphql', $content);
        self::assertStringContainsString('report:runtime-proof', $content);
    }
}
