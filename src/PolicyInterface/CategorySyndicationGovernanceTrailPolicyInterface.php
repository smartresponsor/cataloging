<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationGovernanceTrailReportInterface;

interface CategorySyndicationGovernanceTrailPolicyInterface
{
    public function buildReport(array $policyAwarePayload, array $deliveryPayload, array $historyPayload, array $recoveryPayload): CategorySyndicationGovernanceTrailReportInterface;
}
