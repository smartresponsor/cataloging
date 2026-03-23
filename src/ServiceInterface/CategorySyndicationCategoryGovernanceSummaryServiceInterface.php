<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationCategoryGovernanceSummaryBuiltInterface;

interface CategorySyndicationCategoryGovernanceSummaryServiceInterface
{
    public function buildSummary(string $categoryId, array $trailPayloads, string $actorId, string $reason): CategorySyndicationCategoryGovernanceSummaryBuiltInterface;
}
