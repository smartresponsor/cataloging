<?php

declare(strict_types=1);

namespace App\Tests\Category\E2E;

use App\Command\DumpCategoryTreeCommand;
use App\Command\PublishCategoryCommand;
use App\Infrastructure\CategoryRepositoryStateStore;
use App\InfrastructureInterface\OutboxDispatcherInterface;
use App\Observability\CatalogProjectionMetrics;
use App\Projection\CategoryProjectionRunner;
use App\Repository\CategoryRepository;
use App\Service\CategoryDeliveryPipeline;
use App\Service\CategoryMutationCoordinator;
use App\Service\WebhookDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class CategoryPersistedRuntimeFlowTest extends TestCase
{
    public function testPublishCommandPersistsStateThatNextTreeDumpProcessReads(): void
    {
        $stateFile = sys_get_temp_dir().'/category-runtime-flow-'.bin2hex(random_bytes(4)).'.json';
        @unlink($stateFile);

        $seedRepository = new CategoryRepository();
        $seedRepository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

        $store = new CategoryRepositoryStateStore();
        $store->save($seedRepository, $stateFile);

        $capturedEvents = [];
        $outbox = new class($capturedEvents) implements OutboxDispatcherInterface {
            public array $capturedEvents;

            public function __construct(array &$capturedEvents)
            {
                $this->capturedEvents = &$capturedEvents;
            }

            public function dispatch(array $event): void
            {
                $this->capturedEvents[] = $event;
            }
        };

        $metrics = new CatalogProjectionMetrics();
        $publishRepository = new CategoryRepository();
        $pipeline = new CategoryDeliveryPipeline(
            $outbox,
            new CategoryProjectionRunner($metrics),
            $metrics,
            new WebhookDispatcher(new MockHttpClient(static fn () => new MockResponse('{"ok":true}', ['http_code' => 202])), 'secret-key'),
        );

        $publish = new PublishCategoryCommand(
            new CategoryMutationCoordinator($publishRepository, $pipeline),
            $publishRepository,
            $store,
        );
        $publishTester = new CommandTester($publish);
        $publishStatus = $publishTester->execute(['id' => 'hidden', '--state-file' => $stateFile]);

        self::assertSame(0, $publishStatus);
        self::assertCount(1, $capturedEvents);

        $dump = new DumpCategoryTreeCommand(new CategoryRepository(), $store);
        $dumpTester = new CommandTester($dump);
        $dumpStatus = $dumpTester->execute(['taxonomy' => 'catalog', '--state-file' => $stateFile]);

        self::assertSame(0, $dumpStatus);
        $payload = json_decode(trim($dumpTester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(['root', 'hidden'], $payload['ids']);
        self::assertSame(2, $payload['count']);

        @unlink($stateFile);
    }
}
