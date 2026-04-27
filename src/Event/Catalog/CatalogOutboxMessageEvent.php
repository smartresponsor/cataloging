<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event\Catalog;

/**
 * Represents the outbox message application event.
 */
final class CatalogOutboxMessageEvent
{
    public string $id;
    public string $type;
    public string $payload;
    public string $createdAt;

    /**
     * Initializes the outbox message service collaborators.
     */
    public function __construct(string $id, string $type, string $payload, string $createdAt)
    {
        $this->id = $id;
        $this->type = $type;
        $this->payload = $payload;
        $this->createdAt = $createdAt;
    }
}
