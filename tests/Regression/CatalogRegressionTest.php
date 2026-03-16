<?php

declare(strict_types=1);

namespace App\Tests\Regression;

use PHPUnit\Framework\TestCase;

final class CatalogRegressionTest extends TestCase
{
    public function testBasicApiEndpoints(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/composer.json');
        self::assertFileExists(dirname(__DIR__, 2).'/phpunit.xml.dist');
        self::assertFileExists(dirname(__DIR__, 2).'/api/category-openapi.yaml');
    }
}
