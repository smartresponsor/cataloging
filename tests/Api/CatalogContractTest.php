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
        $this->assertFileExists(__DIR__.'/../../../api/catalog-openapi.yaml');
    }
}
