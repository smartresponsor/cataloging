<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategorySyndicationDestinationGovernanceSummaryBuiltInterface;

interface CatalogSyndicationDestinationGovernanceSummaryServiceInterface
{
    /** @param list<array<string, mixed>> $trailPayloads */
    public function buildSummary(string $destinationId, array $trailPayloads, string $actorId, string $reason): CategorySyndicationDestinationGovernanceSummaryBuiltInterface;
}
