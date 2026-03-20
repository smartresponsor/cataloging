<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationFallbackAwarePackageGateReportInterface;

interface CategorySyndicationFallbackAwarePackageGatePolicyInterface
{
    public function buildReport(array $packageMissingRequiredFields, array $strictMediaRequiredMissing, array $fallbackMediaRequiredMissing, array $warnings, array $strictChecks, array $fallbackChecks, array $exactMatchedBindingIds, array $fallbackMatchedBindingIds): CategorySyndicationFallbackAwarePackageGateReportInterface;
}
