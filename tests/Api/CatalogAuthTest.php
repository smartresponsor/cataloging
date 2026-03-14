<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Api;

use PHPUnit\Framework\TestCase;

final class CatalogAuthTest extends TestCase
{
    public function testSecurityConfigDeclaresCategoryRoles(): void
    {
        $content = file_get_contents(dirname(__DIR__, 2).'/config/packages/security.yaml');

        self::assertIsString($content);
        self::assertStringContainsString('ROLE_CATEGORY_OWNER', $content);
        self::assertStringContainsString('ROLE_CATEGORY_EDITOR', $content);
    }

    public function testCategorySecurityPackageExists(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/config/packages/catalog_security.yaml');
        self::assertFileExists(dirname(__DIR__, 2).'/config/packages/catalog_rbac.yaml');
    }
}
