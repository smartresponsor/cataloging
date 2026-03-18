<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationCategoryGovernanceSummaryBuiltInterface;

interface CategorySyndicationCategoryGovernanceSummaryServiceInterface
{
    public function buildSummary(string $categoryId, array $trailPayloads, string $actorId, string $reason): CategorySyndicationCategoryGovernanceSummaryBuiltInterface;
}
