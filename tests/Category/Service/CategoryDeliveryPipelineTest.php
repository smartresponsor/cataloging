<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Service;

use App\InfrastructureInterface\OutboxDispatcherInterface;
use App\Observability\CatalogProjectionMetrics;
use App\Projection\CategoryProjectionRunner;
use App\Service\CategoryDeliveryPipeline;
use App\Service\WebhookDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class CategoryDeliveryPipelineTest extends TestCase
{
    public function testDeliverRunsOutboxProjectionAndWebhookAsSingleFlow(): void
    {
        $capturedEvent = [];
        $capturedRequest = [];

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

        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$capturedRequest) {
            $capturedRequest = ['method' => $method, 'url' => $url, 'options' => $options];

            return new MockResponse('{"ok":true}', ['http_code' => 202]);
        });

        $metrics = new CatalogProjectionMetrics();
        $metrics->setLag(19);

        $pipeline = new CategoryDeliveryPipeline(
            $outbox,
            new CategoryProjectionRunner($metrics),
            $metrics,
            new WebhookDispatcher($client, 'secret-key'),
        );

        $result = $pipeline->deliver('category.published', ['id' => 'cat-1', 'path' => '/root/phones'], 'https://example.test/webhook');

        self::assertTrue($result['ok']);
        self::assertSame('category.published', $result['eventType']);
        self::assertSame('cat-1', $result['entityId']);
        self::assertSame(0, $result['projectionLag']);
        self::assertTrue($result['delivered']['outbox']);
        self::assertSame('category.published', $capturedEvent['type']);
        self::assertSame('cat-1', $capturedEvent['payload']['id']);
        self::assertSame('POST', $capturedRequest['method']);
        self::assertSame('https://example.test/webhook', $capturedRequest['url']);
    }
}
