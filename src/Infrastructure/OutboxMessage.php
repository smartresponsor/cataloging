<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Infrastructure;

final class OutboxMessage
{
    /** @param array<string,mixed> $payload */
    public function __construct(private readonly array $payload)
    {
    }

    /** @return array<string,mixed> */
    public function payload(): array
    {
        return $this->payload;
    }
}
