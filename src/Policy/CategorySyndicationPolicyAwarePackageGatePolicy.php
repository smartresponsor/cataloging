<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationPolicyAwarePackageGatePolicyInterface;
use App\ValueObject\CategorySyndicationPolicyAwarePackageGateReport;
use App\ValueObjectInterface\CategorySyndicationPolicyAwarePackageGateReportInterface;

final class CategorySyndicationPolicyAwarePackageGatePolicy implements CategorySyndicationPolicyAwarePackageGatePolicyInterface
{
    /**
     * @param list<string>        $packageMissingRequiredFields
     * @param array<string,mixed> $policyPayload
     * @param array<string,mixed> $fallbackGatePayload
     */
    public function buildReport(array $packageMissingRequiredFields, array $policyPayload, array $fallbackGatePayload): CategorySyndicationPolicyAwarePackageGateReportInterface
    {
        $packageMissingRequiredFields = $this->normalizeList($packageMissingRequiredFields);
        $requiredMissing = $this->normalizeList($policyPayload['requiredMissing'] ?? null);
        $warnings = $this->normalizeList(array_merge(
            $this->normalizeList($policyPayload['warnings'] ?? null),
            $this->normalizeList($fallbackGatePayload['warnings'] ?? null),
        ));
        $exactMatchedBindingIds = $this->normalizeList($fallbackGatePayload['exactMatchedBindingIds'] ?? null);
        $fallbackMatchedBindingIds = $this->normalizeList($fallbackGatePayload['fallbackMatchedBindingIds'] ?? null);
        sort($packageMissingRequiredFields);
        sort($requiredMissing);
        sort($warnings);
        sort($exactMatchedBindingIds);
        sort($fallbackMatchedBindingIds);

        $strictPublishable = (bool) ($policyPayload['strictPublishable'] ?? false);
        $fallbackPublishable = (bool) ($policyPayload['fallbackPublishable'] ?? false);
        $resolvedPublishable = (bool) ($policyPayload['resolvedPublishable'] ?? false);
        $fallbackUsed = (bool) ($policyPayload['fallbackUsed'] ?? false);
        $mediaPolicyMode = $this->scalarString($policyPayload['mediaPolicyMode'] ?? 'allow_fallback');

        $checks = [
            'packageFieldsReady' => [] === $packageMissingRequiredFields,
            'policyResolvedPublishable' => $resolvedPublishable,
            'policyStrictPublishable' => $strictPublishable,
            'policyFallbackPublishable' => $fallbackPublishable,
            'policyFallbackUsed' => $fallbackUsed,
            'policyAwarePackageGatePublishable' => false,
        ];
        foreach ($this->boolMap($policyPayload['checks'] ?? null) as $key => $value) {
            $checks['policy:'.$key] = $value;
        }
        foreach ($this->boolMap($fallbackGatePayload['checks'] ?? null) as $key => $value) {
            $checks['fallbackGate:'.$key] = $value;
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

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    /** @return array<string,bool> */
    private function boolMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $key => $item) {
            $normalizedKey = trim((string) $key);
            if ('' === $normalizedKey) {
                continue;
            }
            $result[$normalizedKey] = (bool) $item;
        }

        return $result;
    }

    /** @return list<string> */
    private function normalizeList(mixed $values): array
    {
        if (!is_array($values)) {
            return [];
        }

        $normalized = [];
        foreach ($values as $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $item = trim((string) $value);
            if ('' !== $item) {
                $normalized[] = $item;
            }
        }

        return array_values(array_unique($normalized));
    }
}
