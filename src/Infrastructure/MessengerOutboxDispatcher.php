<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

use App\InfrastructureInterface\OutboxDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerOutboxDispatcher implements OutboxDispatcherInterface
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    /** @param array<string,mixed> $event */
    public function dispatch(array $event): void
    {
        $this->bus->dispatch(new Envelope(new MessengerOutboxMessage($event)));
    }
}
