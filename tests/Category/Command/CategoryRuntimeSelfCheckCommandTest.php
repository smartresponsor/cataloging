<?php

declare(strict_types=1);

namespace App\Tests\Category\Command;

use App\Command\CategoryRuntimeSelfCheckCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryRuntimeSelfCheckCommandTest extends TestCase
{
    public function testSelfCheckReportsHealthyRuntimeSurface(): void
    {
        $tester = new CommandTester(new CategoryRuntimeSelfCheckCommand());
        $status = $tester->execute([]);

        self::assertSame(0, $status);

        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($payload['ok']);
        self::assertSame('category:runtime:self-check', $payload['command']);
        self::assertTrue($payload['runtimeSurfaceHealthy']);
        self::assertGreaterThanOrEqual(6, $payload['passedCount']);
        self::assertSame($payload['totalCount'], $payload['passedCount']);
        self::assertTrue($payload['checks']['runtimeManifestCommand']);
        self::assertTrue($payload['checks']['runtimeGateCommand']);
        self::assertTrue($payload['checks']['runtimeReleaseReportCommand']);
        self::assertTrue($payload['checks']['runtimeRcVerdictCommand']);
    }
}
