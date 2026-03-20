<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationDestinationGovernanceSummaryInterface;

interface CategorySyndicationDestinationGovernanceSummaryPolicyInterface
{
    public function buildSummary(string $destinationId, array $trailPayloads): CategorySyndicationDestinationGovernanceSummaryInterface;
}
