<?php

declare(strict_types=1);

namespace App\Tests\Category\Command;

use App\Command\CategoryRuntimeReleaseReportCommand;
use App\Infrastructure\CategoryRepositoryStateStore;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryRuntimeReleaseReportCommandTest extends TestCase
{
    public function testReleaseReportSummarizesRuntimeReadiness(): void
    {
        $stateFile = sys_get_temp_dir().'/category-runtime-release-report-'.bin2hex(random_bytes(4)).'.json';
        @unlink($stateFile);

        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'live', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Live'], 'slug' => ['en' => 'live'], 'meta' => ['published' => true]],
            ['id' => 'draft', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Draft'], 'slug' => ['en' => 'draft'], 'meta' => ['published' => false]],
        ]);

        $store = new CategoryRepositoryStateStore();
        $store->save($repository, $stateFile);

        $tester = new CommandTester(new CategoryRuntimeReleaseReportCommand(new CategoryRepository(), $store));
        $status = $tester->execute(['taxonomy' => 'catalog', '--state-file' => $stateFile]);

        self::assertSame(0, $status);

        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($payload['ok']);
        self::assertSame('category:runtime:release-report', $payload['command']);
        self::assertTrue($payload['stateLoaded']);
        self::assertSame(['root', 'live'], $payload['publicIds']);
        self::assertTrue($payload['checks']['manifestHasReleaseReport']);
        self::assertTrue($payload['checks']['probeKnowsReleaseReport']);
        self::assertTrue($payload['checks']['gateKnowsReleaseReport']);
        self::assertTrue($payload['checks']['selfCheckKnowsReleaseReport']);
        self::assertTrue($payload['checks']['runtimeRcVerdictCommand']);
        self::assertTrue($payload['checks']['runtimeReleaseEnvelopeCommand']);
        self::assertSame('category:runtime:rc-verdict', $payload['nextLayer']);
        self::assertSame('category:runtime:release-envelope', $payload['handoffLayer']);
        self::assertTrue($payload['releaseCandidateReady']);

        @unlink($stateFile);
    }
}
