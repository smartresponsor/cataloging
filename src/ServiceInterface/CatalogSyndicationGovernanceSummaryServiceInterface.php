<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationCategoryGovernanceSummaryBuiltInterface;

interface CatalogSyndicationGovernanceSummaryServiceInterface
{
    /** @param list<array<string, mixed>> $trailPayloads */
    public function buildSummary(string $categoryId, array $trailPayloads, string $actorId, string $reason): CategorySyndicationCategoryGovernanceSummaryBuiltInterface;
}
