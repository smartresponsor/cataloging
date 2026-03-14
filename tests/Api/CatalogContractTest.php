<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Api;

use PHPUnit\Framework\TestCase;

final class CatalogContractTest extends TestCase
{
    public function testOpenApiExists(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/api/catalog-openapi.yaml');
    }
}
