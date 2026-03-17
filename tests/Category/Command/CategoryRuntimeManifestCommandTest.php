<?php

declare(strict_types=1);

namespace App\Tests\Category\Command;

use App\Command\CategoryRuntimeManifestCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryRuntimeManifestCommandTest extends TestCase
{
    public function testRuntimeManifestReportsKeyRuntimeArtifacts(): void
    {
        $tester = new CommandTester(new CategoryRuntimeManifestCommand());
        $status = $tester->execute([]);

        self::assertSame(0, $status);

        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($payload['ok']);
        self::assertTrue($payload['allFilesPresent']);
        self::assertGreaterThanOrEqual(5, $payload['routesCount']);
        self::assertGreaterThanOrEqual(10, $payload['commandsCount']);
        self::assertSame('category:runtime:manifest', $payload['commands'][2]['name']);
        self::assertSame('category:runtime:probe', $payload['commands'][3]['name']);
        self::assertSame('category:runtime:closure', $payload['commands'][4]['name']);
        self::assertSame('category:runtime:self-check', $payload['commands'][6]['name']);
        self::assertSame('category:runtime:release-report', $payload['commands'][7]['name']);
        self::assertSame('category:runtime:rc-verdict', $payload['commands'][8]['name']);
        self::assertSame('category:runtime:release-envelope', $payload['commands'][9]['name']);
        self::assertSame('/api/admin/category/bulk', $payload['routes'][1]['path']);
        self::assertSame('/admin/category/tree/move', $payload['routes'][4]['path']);
        self::assertSame('api/category-openapi.yaml', $payload['contracts'][0]['file']);
    }
}
