<?php

declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Infrastructure;

use App\InfrastructureInterface\CategoryOutboxDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CategoryMessengerOutboxDispatcher implements CategoryOutboxDispatcherInterface
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    public function dispatch(array $event): void
    {
        $this->bus->dispatch($event);
    }
}
