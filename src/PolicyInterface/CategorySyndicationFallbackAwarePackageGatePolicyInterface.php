<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObjectInterface\CategorySyndicationFallbackAwarePackageGateReportInterface;

/**
 * Defines the contract for category syndication fallback aware package gate policy.
 */
/** @noinspection PhpClassNamingConventionInspection PhpTooManyParametersInspection */
interface CategorySyndicationFallbackAwarePackageGatePolicyInterface
{
    /**
     * @param list<string>       $packageMissingRequiredFields
     * @param list<string>       $strictMediaRequiredMissing
     * @param list<string>       $fallbackMediaRequiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $strictChecks
     * @param array<string,bool> $fallbackChecks
     * @param list<string>       $exactMatchedBindingIds
     * @param list<string>       $fallbackMatchedBindingIds
     */
    /**
     * @noinspection PhpTooManyParametersInspection
     *
     * @param list<string>       $packageMissingRequiredFields
     * @param list<string>       $strictMediaRequiredMissing
     * @param list<string>       $fallbackMediaRequiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $strictChecks
     * @param array<string,bool> $fallbackChecks
     * @param list<string>       $exactMatchedBindingIds
     * @param list<string>       $fallbackMatchedBindingIds
     */
    public function buildReport(
        array $packageMissingRequiredFields,
        array $strictMediaRequiredMissing,
        array $fallbackMediaRequiredMissing,
        array $warnings,
        array $strictChecks,
        array $fallbackChecks,
        array $exactMatchedBindingIds,
        array $fallbackMatchedBindingIds,
    ): CategorySyndicationFallbackAwarePackageGateReportInterface;
}
