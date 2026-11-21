<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event\Catalog;

use App\Cataloging\EventInterface\Catalog\CatalogSyndicationDestinationGovernanceSummaryBuiltEventInterface;

/**
 * Represents the category syndication destination governance summary built application event.
 */
final readonly class CatalogSyndicationDestinationGovernanceSummaryBuiltEvent implements CatalogSyndicationDestinationGovernanceSummaryBuiltEventInterface
{
    /** @param array<string,mixed> $payload */
    public function __construct(
        private array $payload,
        private \DateTimeImmutable $occurredAt,
    ) {
    }

    /** @return array<string,mixed> */
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
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationGovernanceSummaryBuilt', false)) {
    class_alias(CatalogSyndicationDestinationGovernanceSummaryBuiltEvent::class, __NAMESPACE__.'\\SyndicationDestinationGovernanceSummaryBuilt');
}
