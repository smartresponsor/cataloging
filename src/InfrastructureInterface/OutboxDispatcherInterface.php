<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\InfrastructureInterface;
/**
 * Defines the contract for outbox dispatcher.
 */
interface OutboxDispatcherInterface
{
    /** @param array<string,mixed> $event */
    public function dispatch(array $event): void;
}
