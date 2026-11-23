<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObjectInterface\CategorySyndicationCategoryGovernanceSummaryInterface;

interface CategorySyndicationCategoryGovernanceSummaryPolicyInterface
{
    /** @param list<array<string,mixed>> $trailPayloads */
    public function buildSummary(string $categoryId, array $trailPayloads): CategorySyndicationCategoryGovernanceSummaryInterface;
}
