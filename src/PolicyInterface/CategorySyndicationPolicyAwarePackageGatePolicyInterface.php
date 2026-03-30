<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationPolicyAwarePackageGateReportInterface;

interface CategorySyndicationPolicyAwarePackageGatePolicyInterface
{
    /**
     * @param list<string>        $packageMissingRequiredFields
     * @param array<string,mixed> $policyPayload
     * @param array<string,mixed> $fallbackGatePayload
     */
    public function buildReport(array $packageMissingRequiredFields, array $policyPayload, array $fallbackGatePayload): CategorySyndicationPolicyAwarePackageGateReportInterface;
}
