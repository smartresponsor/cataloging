<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationGovernanceTrailPolicyInterface;
use App\ValueObject\CategorySyndicationGovernanceTrailReport;
use App\ValueObjectInterface\CategorySyndicationGovernanceTrailReportInterface;

final class CategorySyndicationGovernanceTrailPolicy implements CategorySyndicationGovernanceTrailPolicyInterface
{
    public function buildReport(array $policyAwarePayload, array $deliveryPayload, array $historyPayload, array $recoveryPayload): CategorySyndicationGovernanceTrailReportInterface
    {
        $mediaPolicyMode = trim((string) ($policyAwarePayload['mediaPolicyMode'] ?? 'strict_exact'));
        $strictPublishable = (bool) ($policyAwarePayload['strictPublishable'] ?? false);
        $fallbackPublishable = (bool) ($policyAwarePayload['fallbackPublishable'] ?? false);
        $resolvedPublishable = (bool) ($policyAwarePayload['resolvedPublishable'] ?? false);
        $fallbackUsed = (bool) ($policyAwarePayload['fallbackUsed'] ?? false);
        $deliveryStatus = trim((string) ($deliveryPayload['status'] ?? 'pending'));
        $retryable = (bool) ($deliveryPayload['retryable'] ?? false);
        $retryScheduled = 'retry_scheduled' === $deliveryStatus || ((int) ($recoveryPayload['scheduledRetries'] ?? 0) > 0);

        $historyCounts = [
            'totalRecords' => (int) ($historyPayload['totalRecords'] ?? 0),
            'deliveredCount' => (int) ($historyPayload['deliveredCount'] ?? 0),
            'failedCount' => (int) ($historyPayload['failedCount'] ?? 0),
            'pendingCount' => (int) ($historyPayload['pendingCount'] ?? 0),
            'retryScheduledCount' => (int) ($historyPayload['retryScheduledCount'] ?? 0),
            'skippedCount' => (int) ($historyPayload['skippedCount'] ?? 0),
        ];

        $warnings = [];
        foreach ([$policyAwarePayload['warnings'] ?? [], $deliveryPayload['warnings'] ?? []] as $warningList) {
            if (!is_array($warningList)) {
                continue;
            }

            foreach ($warningList as $warning) {
                $warning = trim((string) $warning);

                if ('' !== $warning && !in_array($warning, $warnings, true)) {
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
            trim((string) ($policyAwarePayload['destinationId'] ?? $deliveryPayload['destinationId'] ?? $historyPayload['destinationId'] ?? '')),
            trim((string) ($policyAwarePayload['categoryId'] ?? $deliveryPayload['categoryId'] ?? $historyPayload['categoryId'] ?? '')),
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
}
