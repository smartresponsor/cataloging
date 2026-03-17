<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Api;

use PHPUnit\Framework\TestCase;

final class CategoryAuthTest extends TestCase
{
    public function testCategoryApiSecurityConfigProtectsMoveEndpointAndKeepsTreePublic(): void
    {
        $path = dirname(__DIR__, 3).'/config/packages/security.category.api.yaml';
        self::assertFileExists($path);

        $contents = file_get_contents($path);
        self::assertIsString($contents);
        self::assertStringContainsString('pattern: ^/api/category', $contents);
        self::assertStringContainsString('provider: category_jwt', $contents);
        self::assertStringContainsString('path: ^/api/category/tree, roles: IS_AUTHENTICATED_ANONYMOUSLY', $contents);
        self::assertStringContainsString('path: ^/api/category/.+/move, roles: ROLE_ADMIN', $contents);
    }

    public function testCategoryRbacHierarchyDefinesOwnerEditorViewerChain(): void
    {
        $path = dirname(__DIR__, 3).'/config/packages/category_rbac.yaml';
        self::assertFileExists($path);

        $contents = file_get_contents($path);
        self::assertIsString($contents);
        self::assertStringContainsString('category.owner: [category.editor, category.viewer]', $contents);
        self::assertStringContainsString('category.editor: [category.viewer]', $contents);
        self::assertStringContainsString('category.viewer: []', $contents);
    }
}
