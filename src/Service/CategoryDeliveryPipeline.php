<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use App\InfrastructureInterface\OutboxDispatcherInterface;
use App\Observability\CatalogProjectionMetrics;
use App\Projection\CategoryProjectionRunner;

final class CategoryDeliveryPipeline
{
    public function __construct(
        private readonly OutboxDispatcherInterface $outboxDispatcher,
        private readonly CategoryProjectionRunner $projectionRunner,
        private readonly CatalogProjectionMetrics $metrics,
        private readonly WebhookDispatcher $webhookDispatcher,
    ) {
    }

    public function deliver(string $eventType, array $payload, string $endpoint): array
    {
        $event = [
            'id' => (string) ($payload['id'] ?? uniqid('evt-', true)),
            'type' => $eventType,
            'payload' => $payload,
            'createdAt' => gmdate('c'),
        ];

        $this->outboxDispatcher->dispatch($event);
        $this->projectionRunner->runOnce();
        $this->webhookDispatcher->dispatch($eventType, $payload, $endpoint);

        return [
            'ok' => true,
            'eventType' => $eventType,
            'entityId' => (string) ($payload['id'] ?? ''),
            'projectionLag' => $this->metrics->getLag(),
            'delivered' => ['outbox' => true, 'projection' => true, 'webhook' => true],
        ];
    }
}
