<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationPackageGatePolicyInterface;
use App\ValueObject\CategorySyndicationPackageGateReport;
use App\ValueObjectInterface\CategorySyndicationPackageGateReportInterface;

/**
 * Provides the category syndication package gate policy implementation.
 */
final class CategorySyndicationPackageGatePolicy implements CategorySyndicationPackageGatePolicyInterface
{
    /**
     * Builds the report result for the current workflow.
     */
    public function buildReport(
        array $packageMissingRequiredFields,
        array $mediaRequiredMissing,
        array $warnings,
        array $mediaChecks,
        array $matchedBindingIds,
    ): CategorySyndicationPackageGateReportInterface {
        $packageMissingRequiredFields = array_values(
            array_unique(
                array_filter(
                    array_map(static fn (mixed $value): string => trim((string) $value), $packageMissingRequiredFields),
                    static fn (string $value): bool => '' !== $value,
                ),
            ),
        );
        $mediaRequiredMissing = array_values(
            array_unique(
                array_filter(
                    array_map(static fn (mixed $value): string => trim((string) $value), $mediaRequiredMissing),
                    static fn (string $value): bool => '' !== $value,
                ),
            ),
        );
        $warnings = array_values(
            array_unique(
                array_filter(
                    array_map(static fn (mixed $value): string => trim((string) $value), $warnings),
                    static fn (string $value): bool => '' !== $value,
                ),
            ),
        );
        $matchedBindingIds = array_values(
            array_unique(
                array_filter(
                    array_map(static fn (mixed $value): string => trim((string) $value), $matchedBindingIds),
                    static fn (string $value): bool => '' !== $value,
                ),
            ),
        );
        sort($packageMissingRequiredFields);
        sort($mediaRequiredMissing);
        sort($warnings);
        sort($matchedBindingIds);

        $checks = [
            'packageFieldsReady' => [] === $packageMissingRequiredFields,
            'destinationMediaReady' => [] === $mediaRequiredMissing
                && ($mediaChecks['destinationMediaPublishable'] ?? false),
            'packageGatePublishable' => false,
        ];
        foreach ($mediaChecks as $key => $value) {
            $checks[(string) $key] = (bool) $value;
        }

        if (!$checks['packageFieldsReady']) {
            $warnings[] = 'package_missing_required_fields';
        }

        if (!$checks['destinationMediaReady']) {
            $warnings[] = 'destination_media_not_ready';
        }

        $checks['packageGatePublishable'] = $checks['packageFieldsReady'] && $checks['destinationMediaReady'];
        $warnings = array_values(array_unique($warnings));
        sort($warnings);

        return new CategorySyndicationPackageGateReport(
            $packageMissingRequiredFields,
            $mediaRequiredMissing,
            $warnings,
            $checks,
            $matchedBindingIds,
            $checks['packageGatePublishable'],
        );
    }
}
