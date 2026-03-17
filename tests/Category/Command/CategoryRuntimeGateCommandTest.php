<?php

declare(strict_types=1);

namespace App\Tests\Category\Command;

use App\Command\CategoryRuntimeGateCommand;
use App\Infrastructure\CategoryRepositoryStateStore;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryRuntimeGateCommandTest extends TestCase
{
    public function testRuntimeGateReportsCombinedRuntimeClosure(): void
    {
        $stateFile = sys_get_temp_dir().'/category-runtime-gate-'.bin2hex(random_bytes(4)).'.json';
        @unlink($stateFile);

        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'live', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Live'], 'slug' => ['en' => 'live'], 'meta' => ['published' => true]],
            ['id' => 'draft', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Draft'], 'slug' => ['en' => 'draft'], 'meta' => ['published' => false]],
        ]);

        $store = new CategoryRepositoryStateStore();
        $store->save($repository, $stateFile);

        $tester = new CommandTester(new CategoryRuntimeGateCommand(new CategoryRepository(), $store));
        $status = $tester->execute(['taxonomy' => 'catalog', '--state-file' => $stateFile]);

        self::assertSame(0, $status);

        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($payload['ok']);
        self::assertTrue($payload['stateLoaded']);
        self::assertTrue($payload['runtimeGatePassed']);
        self::assertSame(['root', 'live'], $payload['publicIds']);
        self::assertTrue($payload['checks']['manifestCommand']);
        self::assertTrue($payload['checks']['probeCommand']);
        self::assertTrue($payload['checks']['gateCommand']);
        self::assertTrue($payload['checks']['selfCheckCommand']);
        self::assertTrue($payload['checks']['releaseReportCommand']);
        self::assertTrue($payload['checks']['rcVerdictCommand']);

        @unlink($stateFile);
    }
}
