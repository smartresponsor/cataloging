<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity\Api;

use PHPUnit\Framework\TestCase;

final class CategoryContractTest extends TestCase
{
    public function testOpenApiExists(): void
    {
        $this->assertFileExists(__DIR__.'/../../../api/category-openapi.yaml');
    }
}
