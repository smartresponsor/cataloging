<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

use App\InfrastructureInterface\OutboxDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
/**
 * Provides the messenger outbox dispatcher implementation.
 */
final class MessengerOutboxDispatcher implements OutboxDispatcherInterface
{
    /**
     * Initializes the messenger outbox dispatcher service collaborators.
     */
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    /** @param array<string,mixed> $event */
    public function dispatch(array $event): void
    {
        $this->bus->dispatch(new Envelope(new MessengerOutboxMessage($event)));
    }
}
