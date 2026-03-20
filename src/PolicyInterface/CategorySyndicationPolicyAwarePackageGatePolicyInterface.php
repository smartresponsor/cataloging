<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationPolicyAwarePackageGateReportInterface;

interface CategorySyndicationPolicyAwarePackageGatePolicyInterface
{
    public function buildReport(array $packageMissingRequiredFields, array $policyPayload, array $fallbackGatePayload): CategorySyndicationPolicyAwarePackageGateReportInterface;
}
