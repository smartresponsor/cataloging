<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Command;

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

final class PublishCategoryCommandTest extends TestCase
{
    public function testPublishCommandChangesPublicReadStateAndReportsDelivery(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

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
        $pipeline = new CategoryDeliveryPipeline(
            $outbox,
            new CategoryProjectionRunner($metrics),
            $metrics,
            new WebhookDispatcher(new MockHttpClient(static fn () => new MockResponse('{"ok":true}', ['http_code' => 202])), 'secret-key'),
        );

        $command = new PublishCategoryCommand(
            new CategoryMutationCoordinator($repository, $pipeline),
            $repository,
            new CategoryRepositoryStateStore(),
        );

        $tester = new CommandTester($command);
        $status = $tester->execute(['id' => 'hidden']);

        self::assertSame(0, $status);
        self::assertTrue($repository->findById('hidden')['meta']['published']);
        self::assertSame(['root', 'hidden'], array_column($repository->publishedTree('catalog', null, 5, 'en'), 'id'));
        self::assertCount(1, $capturedEvents);
        self::assertStringContainsString('action=publish', $tester->getDisplay());
        self::assertStringContainsString('deliveries=1', $tester->getDisplay());
        self::assertStringContainsString('publicIds=root,hidden', $tester->getDisplay());
    }

    public function testPublishCommandCanLoadAndSaveRepositoryStateFile(): void
    {
        $stateFile = sys_get_temp_dir().'/category-command-state-'.bin2hex(random_bytes(4)).'.json';
        @unlink($stateFile);

        $seedRepository = new CategoryRepository();
        $seedRepository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);
        $store = new CategoryRepositoryStateStore();
        $store->save($seedRepository, $stateFile);

        $workingRepository = new CategoryRepository();
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
        $pipeline = new CategoryDeliveryPipeline(
            $outbox,
            new CategoryProjectionRunner($metrics),
            $metrics,
            new WebhookDispatcher(new MockHttpClient(static fn () => new MockResponse('{"ok":true}', ['http_code' => 202])), 'secret-key'),
        );

        $command = new PublishCategoryCommand(
            new CategoryMutationCoordinator($workingRepository, $pipeline),
            $workingRepository,
            $store,
        );

        $tester = new CommandTester($command);
        $status = $tester->execute(['id' => 'hidden', '--state-file' => $stateFile]);

        self::assertSame(0, $status);
        self::assertStringContainsString('stateFile='.$stateFile, $tester->getDisplay());

        $reloaded = new CategoryRepository();
        $store->load($reloaded, $stateFile);
        self::assertTrue($reloaded->findById('hidden', 'en')['meta']['published']);
        self::assertSame(['root', 'hidden'], array_column($reloaded->publishedTree('catalog', null, 5, 'en'), 'id'));
        self::assertCount(1, $capturedEvents);

        @unlink($stateFile);
    }
}
