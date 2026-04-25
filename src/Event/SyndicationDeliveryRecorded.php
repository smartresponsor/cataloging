<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event;

use App\Cataloging\EventInterface\CatalogSyndicationDeliveryRecordedInterface;

/**
 * Represents the category syndication delivery recorded application event.
 */
final readonly class CatalogSyndicationDeliveryRecorded implements CatalogSyndicationDeliveryRecordedInterface
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
    class_alias(CatalogSyndicationDeliveryRecorded::class, __NAMESPACE__.'\\SyndicationDeliveryRecorded');
}
