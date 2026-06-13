<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\CategoryEntity\GraphQL;

use PHPUnit\Framework\TestCase;

final class CategoryQueryAdvancedTest extends TestCase
{
    public function testGraphQlConfigurationIsPresent(): void
    {
        self::assertFileExists(dirname(__DIR__, 3).'/config/packages/api_platform.yaml');
        $contents = file_get_contents(dirname(__DIR__, 3).'/config/packages/api_platform.yaml');
        self::assertIsString($contents);
        self::assertStringContainsString('graphql', strtolower($contents));
    }
}
