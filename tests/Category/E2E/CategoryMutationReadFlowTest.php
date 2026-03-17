<?php

declare(strict_types=1);

namespace App\Tests\Category\E2E;

use App\Controller\Admin\CategoryBulkController;
use App\Controller\CategoryApiController;
use App\InfrastructureInterface\OutboxDispatcherInterface;
use App\Observability\CatalogProjectionMetrics;
use App\Projection\CategoryProjectionRunner;
use App\Repository\CategoryRepository;
use App\Service\BulkOperator;
use App\Service\CategoryDeliveryPipeline;
use App\Service\WebhookDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;

final class CategoryMutationReadFlowTest extends TestCase
{
    public function testPublishThenBulkUnpublishChangesReadSurfaceAndDeliveryArtifacts(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'electronics', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Electronics'], 'slug' => ['en' => 'electronics'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

        $capturedEvent = [];
        $outbox = new class($capturedEvent) implements OutboxDispatcherInterface {
            public array $capturedEvent;

            public function __construct(array &$capturedEvent)
            {
                $this->capturedEvent = &$capturedEvent;
            }

            public function dispatch(array $event): void
            {
                $this->capturedEvent = $event;
            }
        };

        $metrics = new CatalogProjectionMetrics();
        $metrics->setLag(7);
        $client = new MockHttpClient(static fn () => new MockResponse('{"ok":true}', ['http_code' => 202]));
        $pipeline = new CategoryDeliveryPipeline(
            $outbox,
            new CategoryProjectionRunner($metrics),
            $metrics,
            new WebhookDispatcher($client, 'secret-key'),
        );

        $api = new CategoryApiController($repository, $pipeline);
        $publishRequest = new Request(content: json_encode(['published' => true], JSON_THROW_ON_ERROR));
        $publishResponse = $api->publish('hidden', $publishRequest);
        $publishPayload = json_decode((string) $publishResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertTrue($publishPayload['ok']);
        self::assertTrue($publishPayload['published']);
        self::assertSame('category.published', $publishPayload['delivery']['eventType']);
        self::assertSame('hidden', $capturedEvent['payload']['id']);

        $treePayload = json_decode((string) $api->tree(new Request(['taxonomy' => 'catalog']))->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(3, $treePayload['count']);

        $bulkController = new CategoryBulkController(new BulkOperator($repository));
        $bulkRequest = new Request(content: json_encode(['ids' => ['electronics'], 'action' => 'unpublish'], JSON_THROW_ON_ERROR));
        $bulkPayload = json_decode((string) $bulkController($bulkRequest)->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertTrue($bulkPayload['ok']);
        self::assertSame(1, $bulkPayload['successCount']);

        $afterBulkTree = json_decode((string) $api->tree(new Request(['taxonomy' => 'catalog']))->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(2, $afterBulkTree['count']);
        self::assertSame(['root', 'hidden'], array_column($afterBulkTree['data'], 'id'));
    }
}
