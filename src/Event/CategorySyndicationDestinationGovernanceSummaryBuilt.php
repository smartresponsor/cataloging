<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategorySyndicationDestinationGovernanceSummaryBuiltInterface;
/**
 * Represents the category syndication destination governance summary built application event.
 */
final class CategorySyndicationDestinationGovernanceSummaryBuilt
    implements CategorySyndicationDestinationGovernanceSummaryBuiltInterface
{
    /** @param array<string,mixed> $payload */
    public function __construct(
        private readonly array $payload,
        private readonly \DateTimeImmutable $occurredAt,
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
