<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationPackageGateReportInterface;

interface CategorySyndicationPackageGatePolicyInterface
{
    /**
     * @param list<string>       $packageMissingRequiredFields
     * @param list<string>       $mediaRequiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $mediaChecks
     * @param list<string>       $matchedBindingIds
     */
    public function buildReport(
        array $packageMissingRequiredFields,
        array $mediaRequiredMissing,
        array $warnings,
        array $mediaChecks,
        array $matchedBindingIds,
    ): CategorySyndicationPackageGateReportInterface;
}
