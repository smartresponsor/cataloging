<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event\Catalog;

use App\Cataloging\EventInterface\Catalog\CatalogSyndicationDestinationHistoryBuiltEventInterface;

/**
 * Represents the category syndication destination history built application event.
 */
final readonly class CatalogSyndicationDestinationHistoryBuiltEvent implements CatalogSyndicationDestinationHistoryBuiltEventInterface
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
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationHistoryBuilt', false)) {
    class_alias(CatalogSyndicationDestinationHistoryBuiltEvent::class, __NAMESPACE__.'\\SyndicationDestinationHistoryBuilt');
}
