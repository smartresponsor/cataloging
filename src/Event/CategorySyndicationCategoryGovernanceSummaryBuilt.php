<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategorySyndicationCategoryGovernanceSummaryBuiltInterface;

/**
 * Represents the category syndication category governance summary built application event.
 */
final readonly class CategorySyndicationCategoryGovernanceSummaryBuilt implements CategorySyndicationCategoryGovernanceSummaryBuiltInterface
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
