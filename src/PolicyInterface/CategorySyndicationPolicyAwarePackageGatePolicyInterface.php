<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationPolicyAwarePackageGateReportInterface;

interface CategorySyndicationPolicyAwarePackageGatePolicyInterface
{
    public function buildReport(array $packageMissingRequiredFields, array $policyPayload, array $fallbackGatePayload): CategorySyndicationPolicyAwarePackageGateReportInterface;
}
