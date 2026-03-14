<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 */

namespace App\Event\Outbox;

final class OutboxMessage
{
    public string $id;
    public string $type;
    public string $payload;
    public string $createdAt;

    public function __construct(string $id, string $type, string $payload, string $createdAt)
    {
        $this->id = $id;
        $this->type = $type;
        $this->payload = $payload;
        $this->createdAt = $createdAt;
    }
}
