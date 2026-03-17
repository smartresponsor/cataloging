<?php

declare(strict_types=1);

namespace App\Tests\Category\Api;

use App\Controller\Api\CategoryAdminApiController;
use App\Controller\CategoryApiController;
use App\InfrastructureInterface\OutboxDispatcherInterface;
use App\Observability\CatalogProjectionMetrics;
use App\Projection\CategoryProjectionRunner;
use App\Repository\CategoryRepository;
use App\Service\BulkOperator;
use App\Service\CategoryDeliveryPipeline;
use App\Service\CategoryMutationCoordinator;
use App\Service\WebhookDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;

final class CategoryAdminPublicFlowTest extends TestCase
{
    public function testAdminBulkPublishMakesDraftVisibleOnPublicReadAndEmitsDeliveryProof(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'electronics', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Electronics'], 'slug' => ['en' => 'electronics'], 'meta' => ['published' => true]],
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
        $metrics->setLag(3);
        $pipeline = new CategoryDeliveryPipeline(
            $outbox,
            new CategoryProjectionRunner($metrics),
            $metrics,
            new WebhookDispatcher(new MockHttpClient(static fn () => new MockResponse('{"ok":true}', ['http_code' => 202])), 'secret-key'),
        );

        $admin = new CategoryAdminApiController(
            new BulkOperator($repository),
            $repository,
            new CategoryMutationCoordinator($repository, $pipeline),
        );
        $public = new CategoryApiController($repository, $pipeline);

        $before = json_decode((string) $public->tree(new Request(['taxonomy' => 'catalog']))->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(['root', 'electronics'], array_column($before['data'], 'id'));

        $bulkResponse = $admin->bulk(new Request(content: json_encode(['ids' => ['hidden'], 'action' => 'publish'], JSON_THROW_ON_ERROR)));
        $bulkPayload = json_decode((string) $bulkResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertTrue($bulkPayload['ok']);
        self::assertSame(1, $bulkPayload['successCount']);
        self::assertSame(1, $bulkPayload['deliveryCount']);
        self::assertSame('category.published', $bulkPayload['result']['deliveries'][0]['eventType']);

        $after = json_decode((string) $public->tree(new Request(['taxonomy' => 'catalog']))->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(['root', 'electronics', 'hidden'], array_column($after['data'], 'id'));
        self::assertCount(1, $capturedEvents);
        self::assertSame('hidden', $capturedEvents[0]['payload']['id']);
    }

    public function testAdminBulkUnpublishRemovesCategoryFromPublicReadAndReturnsPublicProof(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'electronics', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Electronics'], 'slug' => ['en' => 'electronics'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => true]],
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

        $admin = new CategoryAdminApiController(
            new BulkOperator($repository),
            $repository,
            new CategoryMutationCoordinator($repository, $pipeline),
        );
        $public = new CategoryApiController($repository, $pipeline);

        $bulkResponse = $admin->bulk(new Request(content: json_encode(['ids' => ['hidden'], 'action' => 'unpublish'], JSON_THROW_ON_ERROR)));
        $bulkPayload = json_decode((string) $bulkResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertTrue($bulkPayload['ok']);
        self::assertSame(1, $bulkPayload['successCount']);
        self::assertSame(1, $bulkPayload['deliveryCount']);
        self::assertSame(['root', 'electronics'], $bulkPayload['publicIdsAfter']);

        $after = json_decode((string) $public->tree(new Request(['taxonomy' => 'catalog']))->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(['root', 'electronics'], array_column($after['data'], 'id'));
        self::assertCount(1, $capturedEvents);
        self::assertSame('category.unpublished', $bulkPayload['result']['deliveries'][0]['eventType']);
    }
}
