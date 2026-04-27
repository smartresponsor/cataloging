<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event\Catalog;

use App\Cataloging\EventInterface\Catalog\CatalogCategorySyndicationRetryScheduledEventInterface;

/**
 * Represents the category syndication retry scheduled application event.
 */
final readonly class CatalogCategorySyndicationRetryScheduledEvent implements CatalogCategorySyndicationRetryScheduledEventInterface
{
    /**
     * @param array<string,mixed> $payload
     */
    public function __construct(
        private array $payload,
        private \DateTimeImmutable $occurredAt,
    ) {
    }

    /**
     * Handles the payload workflow.
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * Handles the occurred at workflow.
     */
    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
