<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationDestinationGovernanceSummaryBuiltInterface;
/**
 * Defines the contract for catalog syndication destination governance summary service.
 */
interface CatalogSyndicationDestinationGovernanceSummaryServiceInterface
{
    /** @param list<array<string, mixed>> $trailPayloads */
    public function buildSummary(
        string $destinationId,
        array $trailPayloads,
        string $actorId,
        string $reason,
    ): CategorySyndicationDestinationGovernanceSummaryBuiltInterface;
}
