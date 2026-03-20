<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationPolicyAwarePackageGatePolicyInterface;
use App\ValueObject\CategorySyndicationPolicyAwarePackageGateReport;
use App\ValueObjectInterface\CategorySyndicationPolicyAwarePackageGateReportInterface;

final class CategorySyndicationPolicyAwarePackageGatePolicy implements CategorySyndicationPolicyAwarePackageGatePolicyInterface
{
    public function buildReport(array $packageMissingRequiredFields, array $policyPayload, array $fallbackGatePayload): CategorySyndicationPolicyAwarePackageGateReportInterface
    {
        $normalize = static fn (array $values): array => array_values(array_unique(array_filter(array_map(static fn (mixed $value): string => trim((string) $value), $values), static fn (string $value): bool => '' !== $value)));

        $packageMissingRequiredFields = $normalize($packageMissingRequiredFields);
        $requiredMissing = $normalize(is_array($policyPayload['requiredMissing'] ?? null) ? $policyPayload['requiredMissing'] : []);
        $warnings = $normalize(array_merge(
            is_array($policyPayload['warnings'] ?? null) ? $policyPayload['warnings'] : [],
            is_array($fallbackGatePayload['warnings'] ?? null) ? $fallbackGatePayload['warnings'] : [],
        ));
        $exactMatchedBindingIds = $normalize(is_array($fallbackGatePayload['exactMatchedBindingIds'] ?? null) ? $fallbackGatePayload['exactMatchedBindingIds'] : []);
        $fallbackMatchedBindingIds = $normalize(is_array($fallbackGatePayload['fallbackMatchedBindingIds'] ?? null) ? $fallbackGatePayload['fallbackMatchedBindingIds'] : []);
        sort($packageMissingRequiredFields);
        sort($requiredMissing);
        sort($warnings);
        sort($exactMatchedBindingIds);
        sort($fallbackMatchedBindingIds);

        $strictPublishable = (bool) ($policyPayload['strictPublishable'] ?? false);
        $fallbackPublishable = (bool) ($policyPayload['fallbackPublishable'] ?? false);
        $resolvedPublishable = (bool) ($policyPayload['resolvedPublishable'] ?? false);
        $fallbackUsed = (bool) ($policyPayload['fallbackUsed'] ?? false);
        $mediaPolicyMode = trim((string) ($policyPayload['mediaPolicyMode'] ?? 'allow_fallback'));

        $checks = [
            'packageFieldsReady' => [] === $packageMissingRequiredFields,
            'policyResolvedPublishable' => $resolvedPublishable,
            'policyStrictPublishable' => $strictPublishable,
            'policyFallbackPublishable' => $fallbackPublishable,
            'policyFallbackUsed' => $fallbackUsed,
            'policyAwarePackageGatePublishable' => false,
        ];
        foreach ((array) ($policyPayload['checks'] ?? []) as $key => $value) {
            $checks['policy:'.(string) $key] = (bool) $value;
        }
        foreach ((array) ($fallbackGatePayload['checks'] ?? []) as $key => $value) {
            $checks['fallbackGate:'.(string) $key] = (bool) $value;
        }

        if (!$checks['packageFieldsReady']) {
            $warnings[] = 'package_missing_required_fields';
        }
        if (!$checks['policyResolvedPublishable']) {
            $warnings[] = 'destination_media_policy_not_publishable';
        }
        if ($fallbackUsed && $resolvedPublishable) {
            $warnings[] = 'package_publishable_by_destination_media_policy_fallback';
        }

        $warnings = array_values(array_unique($warnings));
        sort($warnings);
        $checks['policyAwarePackageGatePublishable'] = $checks['packageFieldsReady'] && $checks['policyResolvedPublishable'];

        return new CategorySyndicationPolicyAwarePackageGateReport(
            $mediaPolicyMode,
            $packageMissingRequiredFields,
            $requiredMissing,
            $warnings,
            $checks,
            $exactMatchedBindingIds,
            $fallbackMatchedBindingIds,
            $strictPublishable,
            $fallbackPublishable,
            $checks['policyAwarePackageGatePublishable'],
            $fallbackUsed,
        );
    }
}
