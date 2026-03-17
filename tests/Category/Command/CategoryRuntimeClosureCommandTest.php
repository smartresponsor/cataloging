<?php

declare(strict_types=1);

namespace App\Tests\Category\Command;

use App\Command\CategoryRuntimeClosureCommand;
use App\Infrastructure\CategoryRepositoryStateStore;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryRuntimeClosureCommandTest extends TestCase
{
    public function testClosureReportsRuntimeChecksAndPersistedPublicTruth(): void
    {
        $stateFile = sys_get_temp_dir().'/category-runtime-closure-'.bin2hex(random_bytes(4)).'.json';
        @unlink($stateFile);

        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'draft', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Draft'], 'slug' => ['en' => 'draft'], 'meta' => ['published' => false]],
            ['id' => 'live', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Live'], 'slug' => ['en' => 'live'], 'meta' => ['published' => true]],
        ]);

        $store = new CategoryRepositoryStateStore();
        $store->save($repository, $stateFile);

        $tester = new CommandTester(new CategoryRuntimeClosureCommand(new CategoryRepository(), $store));
        $status = $tester->execute(['taxonomy' => 'catalog', '--state-file' => $stateFile]);

        self::assertSame(0, $status);

        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($payload['ok']);
        self::assertTrue($payload['stateLoaded']);
        self::assertSame(['root', 'live'], $payload['publicIds']);
        self::assertTrue($payload['allChecksPassed']);
        self::assertTrue($payload['checks']['adminMoveRouteYaml']);
        self::assertTrue($payload['checks']['adminMoveController']);
        self::assertTrue($payload['checks']['runtimeManifestCommand']);
        self::assertTrue($payload['checks']['runtimeProbeCommand']);

        @unlink($stateFile);
    }
}
