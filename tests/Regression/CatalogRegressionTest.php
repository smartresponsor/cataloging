<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Regression;

use PHPUnit\Framework\TestCase;

final class CatalogRegressionTest extends TestCase
{
    public function testCanonicalRuntimeFilesExist(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/phpunit.xml');
        self::assertFileExists(dirname(__DIR__, 2).'/composer.json');
        self::assertFileExists(dirname(__DIR__, 2).'/src/Kernel.php');
    }
}
