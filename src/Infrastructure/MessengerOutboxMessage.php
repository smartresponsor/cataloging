<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Infrastructure;

/**
 * Provides the messenger outbox message implementation.
 */
final readonly class MessengerOutboxMessage
{
    /** @param array<string,mixed> $payload */
    public function __construct(private array $payload)
    {
    }

    /** @return array<string,mixed> */
    public function payload(): array
    {
        return $this->payload;
    }
}
