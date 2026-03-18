<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationFallbackAwarePackageGatePolicyInterface;
use App\ValueObject\CategorySyndicationFallbackAwarePackageGateReport;
use App\ValueObjectInterface\CategorySyndicationFallbackAwarePackageGateReportInterface;

final class CategorySyndicationFallbackAwarePackageGatePolicy implements CategorySyndicationFallbackAwarePackageGatePolicyInterface
{
    public function buildReport(array $packageMissingRequiredFields, array $strictMediaRequiredMissing, array $fallbackMediaRequiredMissing, array $warnings, array $strictChecks, array $fallbackChecks, array $exactMatchedBindingIds, array $fallbackMatchedBindingIds): CategorySyndicationFallbackAwarePackageGateReportInterface
    {
        $normalize = static fn (array $values): array => array_values(array_unique(array_filter(array_map(static fn (mixed $value): string => trim((string) $value), $values), static fn (string $value): bool => '' !== $value)));

        $packageMissingRequiredFields = $normalize($packageMissingRequiredFields);
        $strictMediaRequiredMissing = $normalize($strictMediaRequiredMissing);
        $fallbackMediaRequiredMissing = $normalize($fallbackMediaRequiredMissing);
        $warnings = $normalize($warnings);
        $exactMatchedBindingIds = $normalize($exactMatchedBindingIds);
        $fallbackMatchedBindingIds = $normalize($fallbackMatchedBindingIds);
        sort($packageMissingRequiredFields);
        sort($strictMediaRequiredMissing);
        sort($fallbackMediaRequiredMissing);
        sort($warnings);
        sort($exactMatchedBindingIds);
        sort($fallbackMatchedBindingIds);

        $checks = [
            'packageFieldsReady' => [] === $packageMissingRequiredFields,
            'strictDestinationMediaReady' => [] === $strictMediaRequiredMissing && (bool) ($strictChecks['destinationMediaPublishable'] ?? false),
            'fallbackDestinationMediaReady' => [] === $fallbackMediaRequiredMissing && (bool) ($fallbackChecks['destinationMediaReadyWithFallback'] ?? false),
            'fallbackUsed' => (bool) ($fallbackChecks['fallbackUsed'] ?? false),
            'strictPackageGatePublishable' => false,
            'fallbackPackageGatePublishable' => false,
        ];

        foreach ($strictChecks as $key => $value) {
            $checks['strict:'.(string) $key] = (bool) $value;
        }
        foreach ($fallbackChecks as $key => $value) {
            $checks['fallback:'.(string) $key] = (bool) $value;
        }

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

        $checks['strictPackageGatePublishable'] = $checks['packageFieldsReady'] && $checks['strictDestinationMediaReady'];
        $checks['fallbackPackageGatePublishable'] = $checks['packageFieldsReady'] && $checks['fallbackDestinationMediaReady'];
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
}
