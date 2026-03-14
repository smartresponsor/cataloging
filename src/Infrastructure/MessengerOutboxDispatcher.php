<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Infrastructure;

use App\InfrastructureInterface\OutboxDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerOutboxDispatcher implements OutboxDispatcherInterface
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    /**
     * @param array<string, mixed> $event
     */
    public function dispatch(array $event): void
    {
        $this->bus->dispatch($event);
    }
}
