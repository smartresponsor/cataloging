<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationCategoryGovernanceSummaryInterface;

interface CategorySyndicationCategoryGovernanceSummaryPolicyInterface
{
    public function buildSummary(string $categoryId, array $trailPayloads): CategorySyndicationCategoryGovernanceSummaryInterface;
}
