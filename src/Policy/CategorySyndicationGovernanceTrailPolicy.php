<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationGovernanceTrailPolicyInterface;
use App\ValueObject\CategorySyndicationGovernanceTrailReport;
use App\ValueObjectInterface\CategorySyndicationGovernanceTrailReportInterface;
/**
 * Provides the category syndication governance trail policy implementation.
 */
final class CategorySyndicationGovernanceTrailPolicy implements CategorySyndicationGovernanceTrailPolicyInterface
{
    /**
     * @param array<string,mixed> $policyAwarePayload
     * @param array<string,mixed> $deliveryPayload
     * @param array<string,mixed> $historyPayload
     * @param array<string,mixed> $recoveryPayload
     */
    public function buildReport(
        array $policyAwarePayload,
        array $deliveryPayload,
        array $historyPayload,
        array $recoveryPayload,
    ): CategorySyndicationGovernanceTrailReportInterface
    {
        $mediaPolicyMode = $this->scalarString($policyAwarePayload['mediaPolicyMode'] ?? 'strict_exact');
        $strictPublishable = (bool) ($policyAwarePayload['strictPublishable'] ?? false);
        $fallbackPublishable = (bool) ($policyAwarePayload['fallbackPublishable'] ?? false);
        $resolvedPublishable = (bool) ($policyAwarePayload['resolvedPublishable'] ?? false);
        $fallbackUsed = (bool) ($policyAwarePayload['fallbackUsed'] ?? false);
        $deliveryStatus = $this->scalarString($deliveryPayload['status'] ?? 'pending');
        $retryable = (bool) ($deliveryPayload['retryable'] ?? false);
        $retryScheduled = 'retry_scheduled' === $deliveryStatus
            || ($this->intValue($recoveryPayload['scheduledRetries'] ?? null) > 0);

        $historyCounts = [
            'totalRecords' => $this->intValue($historyPayload['totalRecords'] ?? null),
            'deliveredCount' => $this->intValue($historyPayload['deliveredCount'] ?? null),
            'failedCount' => $this->intValue($historyPayload['failedCount'] ?? null),
            'pendingCount' => $this->intValue($historyPayload['pendingCount'] ?? null),
            'retryScheduledCount' => $this->intValue($historyPayload['retryScheduledCount'] ?? null),
            'skippedCount' => $this->intValue($historyPayload['skippedCount'] ?? null),
        ];

        $warnings = [];
        foreach ([$policyAwarePayload['warnings'] ?? null, $deliveryPayload['warnings'] ?? null] as $warningList) {
            foreach ($this->stringList($warningList) as $warning) {
                if (!in_array($warning, $warnings, true)) {
                    $warnings[] = $warning;
                }
            }
        }

        if ($fallbackUsed) {
            $warnings[] = 'governance_trail_fallback_used';
        }

        if ($retryable && $historyCounts['failedCount'] > 0) {
            $warnings[] = 'governance_trail_retryable_failures_present';
        }

        if (!$resolvedPublishable) {
            $warnings[] = 'governance_trail_not_publishable';
        }

        $warnings = array_values(array_unique($warnings));

        $checks = [
            'governanceTrailResolvedPublishable' => $resolvedPublishable,
            'governanceTrailFallbackUsed' => $fallbackUsed,
            'governanceTrailRetryable' => $retryable,
            'governanceTrailRetryScheduled' => $retryScheduled,
            'governanceTrailHasFailures' => $historyCounts['failedCount'] > 0,
            'governanceTrailHasDelivered' => $historyCounts['deliveredCount'] > 0,
        ];

        return new CategorySyndicationGovernanceTrailReport(
            $this->scalarString(
                $policyAwarePayload['destinationId']
                ?? $deliveryPayload['destinationId']
                ?? $historyPayload['destinationId']
                ?? null,
            ),
            $this->scalarString(
                $policyAwarePayload['categoryId']
                ?? $deliveryPayload['categoryId']
                ?? $historyPayload['categoryId']
                ?? null,
            ),
            $mediaPolicyMode,
            $strictPublishable,
            $fallbackPublishable,
            $resolvedPublishable,
            $fallbackUsed,
            $deliveryStatus,
            $retryable,
            $retryScheduled,
            $historyCounts,
            $warnings,
            $checks,
        );
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function intValue(mixed $value): int
    {
        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0);
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $normalized = trim((string) $item);
            if ('' !== $normalized) {
                $result[] = $normalized;
            }
        }

        return array_values($result);
    }
}
