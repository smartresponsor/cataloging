<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Api;

use App\Controller\CategoryApiController;
use App\InfrastructureInterface\OutboxDispatcherInterface;
use App\Observability\CatalogProjectionMetrics;
use App\Projection\CategoryProjectionRunner;
use App\Repository\CategoryRepository;
use App\Service\CategoryDeliveryPipeline;
use App\Service\WebhookDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;

final class CategoryApiControllerTest extends TestCase
{
    public function testTreeReturnsCanonicalEnvelopeAndFiltersUnpublishedByDefault(): void
    {
        $controller = new CategoryApiController();

        $response = $controller->tree(new Request(['locale' => 'uk', 'taxonomy' => 'catalog', 'depth' => 3]));
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($payload['ok']);
        self::assertSame('catalog', $payload['taxonomy']);
        self::assertSame('uk', $payload['locale']);
        self::assertSame(2, $payload['count']);
        self::assertSame('Корінь', $payload['data'][0]['name']);
        self::assertSame('electronics', $payload['data'][1]['id']);
    }

    public function testMoveRejectsMissingParentIdWithCanonicalErrorEnvelope(): void
    {
        $controller = new CategoryApiController();
        $request = new Request(content: json_encode([], JSON_THROW_ON_ERROR));

        $response = $controller->move('cat-1', $request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(400, $response->getStatusCode());
        self::assertFalse($payload['ok']);
        self::assertSame('cat-1', $payload['nodeId']);
        self::assertSame(['parent_id is required'], $payload['errors']);
    }

    public function testMoveReturnsCanonicalMutationEnvelope(): void
    {
        $controller = new CategoryApiController();
        $request = new Request(content: json_encode(['parent_id' => 'root', 'order' => 4], JSON_THROW_ON_ERROR));

        $response = $controller->move('electronics', $request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($payload['ok']);
        self::assertSame('move', $payload['action']);
        self::assertSame('electronics', $payload['nodeId']);
        self::assertSame('root', $payload['parentId']);
        self::assertSame(4, $payload['order']);
        self::assertStringContainsString('/root/electronics', (string) $payload['path']);
    }

    public function testPublishAcceptsValidPublishedFlagAndReturnsState(): void
    {
        $controller = new CategoryApiController();
        $request = new Request(content: json_encode(['published' => true], JSON_THROW_ON_ERROR));

        $response = $controller->publish('hidden', $request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($payload['ok']);
        self::assertSame('publish', $payload['action']);
        self::assertSame('published', $payload['state']);
        self::assertTrue($payload['published']);
        self::assertSame('hidden', $payload['data']['id']);
        self::assertTrue($payload['data']['meta']['published']);
    }

    public function testPublishRejectsNonBooleanFlag(): void
    {
        $controller = new CategoryApiController();
        $request = new Request(content: json_encode(['published' => 'yes'], JSON_THROW_ON_ERROR));

        $response = $controller->publish('cat-1', $request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(400, $response->getStatusCode());
        self::assertFalse($payload['ok']);
        self::assertSame(['published must be boolean'], $payload['errors']);
    }

    public function testPublishTriggersDeliveryPipelineWhenInjected(): void
    {
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

        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

        $metrics = new CatalogProjectionMetrics();
        $metrics->setLag(5);
        $pipeline = new CategoryDeliveryPipeline(
            $outbox,
            new CategoryProjectionRunner($metrics),
            $metrics,
            new WebhookDispatcher(new MockHttpClient(static fn () => new MockResponse('{"ok":true}', ['http_code' => 202])), 'secret-key'),
        );

        $controller = new CategoryApiController($repository, $pipeline);
        $response = $controller->publish('hidden', new Request(content: json_encode(['published' => true], JSON_THROW_ON_ERROR)));
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertTrue($payload['ok']);
        self::assertSame('category.published', $payload['delivery']['eventType']);
        self::assertSame('hidden', $capturedEvent['payload']['id']);
        self::assertSame(0, $payload['delivery']['projectionLag']);
    }
}
