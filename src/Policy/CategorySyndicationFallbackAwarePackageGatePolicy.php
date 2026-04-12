<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationFallbackAwarePackageGatePolicyInterface;
use App\ValueObject\CategorySyndicationFallbackAwarePackageGateReport;
use App\ValueObjectInterface\CategorySyndicationFallbackAwarePackageGateReportInterface;

/**
 * Provides the category syndication fallback aware package gate policy implementation.
 */
final class CategorySyndicationFallbackAwarePackageGatePolicy implements CategorySyndicationFallbackAwarePackageGatePolicyInterface
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
    ): CategorySyndicationFallbackAwarePackageGateReportInterface {
        $packageMissingRequiredFields = $this->normalizeList($packageMissingRequiredFields);
        $strictMediaRequiredMissing = $this->normalizeList($strictMediaRequiredMissing);
        $fallbackMediaRequiredMissing = $this->normalizeList($fallbackMediaRequiredMissing);
        $warnings = $this->normalizeList($warnings);
        $exactMatchedBindingIds = $this->normalizeList($exactMatchedBindingIds);
        $fallbackMatchedBindingIds = $this->normalizeList($fallbackMatchedBindingIds);
        sort($packageMissingRequiredFields);
        sort($strictMediaRequiredMissing);
        sort($fallbackMediaRequiredMissing);
        sort($warnings);
        sort($exactMatchedBindingIds);
        sort($fallbackMatchedBindingIds);

        $checks = [
            'packageFieldsReady' => [] === $packageMissingRequiredFields,
            'strictDestinationMediaReady' => [] === $strictMediaRequiredMissing
                && ($strictChecks['destinationMediaPublishable'] ?? false),
            'fallbackDestinationMediaReady' => [] === $fallbackMediaRequiredMissing
                && ($fallbackChecks['destinationMediaReadyWithFallback'] ?? false),
            'fallbackUsed' => $fallbackChecks['fallbackUsed'] ?? false,
            'strictPackageGatePublishable' => false,
            'fallbackPackageGatePublishable' => false,
        ];

        $prefixedStrictChecks = [];
        foreach ($strictChecks as $checkName => $checkValue) {
            $prefixedStrictChecks['strict:'.$checkName] = $checkValue;
        }
        $prefixedFallbackChecks = [];
        foreach ($fallbackChecks as $checkName => $checkValue) {
            $prefixedFallbackChecks['fallback:'.$checkName] = $checkValue;
        }
        $checks = array_replace($checks, $prefixedStrictChecks, $prefixedFallbackChecks);

        if (!$checks['packageFieldsReady']) {
            $warnings[] = 'package_missing_required_fields';
        }
        if (!$checks['strictDestinationMediaReady']) {
            $warnings[] = 'strict_destination_media_not_ready';
        }
        if (!$checks['fallbackDestinationMediaReady']) {
            $warnings[] = 'fallback_destination_media_not_ready';
        }
        if ($checks['fallbackUsed']) {
            $warnings[] = 'package_publishable_via_fallback_only';
        }

        $checks['strictPackageGatePublishable'] =
            $checks['packageFieldsReady']
            && $checks['strictDestinationMediaReady'];
        $checks['fallbackPackageGatePublishable'] =
            $checks['packageFieldsReady']
            && $checks['fallbackDestinationMediaReady'];
        $warnings = array_values(array_unique($warnings));
        sort($warnings);

        return new CategorySyndicationFallbackAwarePackageGateReport(
            $packageMissingRequiredFields,
            $strictMediaRequiredMissing,
            $fallbackMediaRequiredMissing,
            $warnings,
            $checks,
            $exactMatchedBindingIds,
            $fallbackMatchedBindingIds,
            $checks['strictPackageGatePublishable'],
            $checks['fallbackPackageGatePublishable'],
        );
    }

    /**
     * @noinspection PhpTooManyParametersInspection
     *
     * @param array<int,string> $values
     *
     * @return list<string>
     */
    private function normalizeList(array $values): array
    {
        $normalized = [];
        foreach ($values as $value) {
            $item = trim($value);
            if ('' !== $item) {
                $normalized[] = $item;
            }
        }

        return array_values(array_unique($normalized));
    }
}
