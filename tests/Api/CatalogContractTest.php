<?php

declare(strict_types=1);

namespace App\Tests\Api;

use PHPUnit\Framework\TestCase;

final class CatalogContractTest extends TestCase
{
    public function testOpenApiExists(): void
    {
        self::assertFileExists(__DIR__.'/../../api/category-openapi.yaml');
    }
}
