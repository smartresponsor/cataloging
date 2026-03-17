<?php

declare(strict_types=1);

namespace App\Tests\Category\Command;

use App\Command\CategoryRuntimeReleaseEnvelopeCommand;
use App\Infrastructure\CategoryRepositoryStateStore;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryRuntimeReleaseEnvelopeCommandTest extends TestCase
{
    public function testReleaseEnvelopeSummarizesFinalRuntimeHandoffReadiness(): void
    {
        $stateFile = sys_get_temp_dir().'/category-runtime-release-envelope-'.bin2hex(random_bytes(4)).'.json';
        @unlink($stateFile);

        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'live', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Live'], 'slug' => ['en' => 'live'], 'meta' => ['published' => true]],
            ['id' => 'draft', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Draft'], 'slug' => ['en' => 'draft'], 'meta' => ['published' => false]],
        ]);

        $store = new CategoryRepositoryStateStore();
        $store->save($repository, $stateFile);

        $tester = new CommandTester(new CategoryRuntimeReleaseEnvelopeCommand(new CategoryRepository(), $store));
        $status = $tester->execute(['taxonomy' => 'catalog', '--state-file' => $stateFile]);

        self::assertSame(0, $status);

        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($payload['ok']);
        self::assertSame('category:runtime:release-envelope', $payload['command']);
        self::assertTrue($payload['stateLoaded']);
        self::assertSame(['root', 'live'], $payload['publicIds']);
        self::assertTrue($payload['checks']['manifestKnowsReleaseEnvelope']);
        self::assertTrue($payload['checks']['probeKnowsReleaseEnvelope']);
        self::assertTrue($payload['checks']['gateKnowsReleaseEnvelope']);
        self::assertTrue($payload['checks']['selfCheckKnowsReleaseEnvelope']);
        self::assertTrue($payload['checks']['releaseReportKnowsReleaseEnvelope']);
        self::assertTrue($payload['checks']['rcVerdictKnowsReleaseEnvelope']);
        self::assertTrue($payload['handoffReady']);
        self::assertSame('release-envelope-ready', $payload['verdict']);

        @unlink($stateFile);
    }
}
