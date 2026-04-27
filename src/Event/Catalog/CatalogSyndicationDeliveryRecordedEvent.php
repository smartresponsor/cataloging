<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event\Catalog;

use App\Cataloging\EventInterface\Catalog\CatalogSyndicationDeliveryRecordedEventInterface;

/**
 * Represents the category syndication delivery recorded application event.
 */
final readonly class CatalogSyndicationDeliveryRecordedEvent implements CatalogSyndicationDeliveryRecordedEventInterface
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
if (!class_exists(__NAMESPACE__.'\\SyndicationDeliveryRecorded', false)) {
    class_alias(CatalogSyndicationDeliveryRecordedEvent::class, __NAMESPACE__.'\\SyndicationDeliveryRecorded');
}
