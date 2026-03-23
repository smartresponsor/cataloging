<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationFallbackAwarePackageGateReportInterface;

interface CategorySyndicationFallbackAwarePackageGatePolicyInterface
{
    public function buildReport(array $packageMissingRequiredFields, array $strictMediaRequiredMissing, array $fallbackMediaRequiredMissing, array $warnings, array $strictChecks, array $fallbackChecks, array $exactMatchedBindingIds, array $fallbackMatchedBindingIds): CategorySyndicationFallbackAwarePackageGateReportInterface;
}
