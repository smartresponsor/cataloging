<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationDestinationGovernanceSummaryBuiltInterface;

interface CatalogSyndicationDestinationGovernanceSummaryServiceInterface
{
    public function buildSummary(string $destinationId, array $trailPayloads, string $actorId, string $reason): CategorySyndicationDestinationGovernanceSummaryBuiltInterface;
}
