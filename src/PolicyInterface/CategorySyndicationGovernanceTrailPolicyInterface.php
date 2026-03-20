<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationGovernanceTrailReportInterface;

interface CategorySyndicationGovernanceTrailPolicyInterface
{
    public function buildReport(array $policyAwarePayload, array $deliveryPayload, array $historyPayload, array $recoveryPayload): CategorySyndicationGovernanceTrailReportInterface;
}
