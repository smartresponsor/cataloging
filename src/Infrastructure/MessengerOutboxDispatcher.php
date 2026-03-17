<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Infrastructure;

use App\Event\Outbox\OutboxMessage;
use App\InfrastructureInterface\OutboxDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerOutboxDispatcher implements OutboxDispatcherInterface
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    public function dispatch(array $event): void
    {
        $message = new OutboxMessage(
            (string) ($event['id'] ?? ''),
            (string) ($event['type'] ?? 'category.unknown'),
            json_encode($event['payload'] ?? $event, JSON_THROW_ON_ERROR),
            (string) ($event['createdAt'] ?? gmdate('c')),
        );

        $this->bus->dispatch($message);
    }
}
