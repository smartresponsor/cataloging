<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationDestinationGovernanceSummaryInterface;

interface CategorySyndicationDestinationGovernanceSummaryPolicyInterface
{
    /** @param list<array<string,mixed>> $trailPayloads */
    public function buildSummary(string $destinationId, array $trailPayloads): CategorySyndicationDestinationGovernanceSummaryInterface;
}
