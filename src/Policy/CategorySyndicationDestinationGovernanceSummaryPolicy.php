<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationDestinationGovernanceSummaryPolicyInterface;
use App\ValueObject\CategorySyndicationDestinationGovernanceSummary;
use App\ValueObjectInterface\CategorySyndicationDestinationGovernanceSummaryInterface;

final class CategorySyndicationDestinationGovernanceSummaryPolicy implements CategorySyndicationDestinationGovernanceSummaryPolicyInterface
{
    public function buildSummary(string $destinationId, array $trailPayloads): CategorySyndicationDestinationGovernanceSummaryInterface
    {
        $statusCounts = [
            'pending' => 0,
            'delivered' => 0,
            'failed' => 0,
            'retry_scheduled' => 0,
            'skipped' => 0,
        ];
        $policyModeCounts = [
            'strict_exact' => 0,
            'allow_fallback' => 0,
            'prefer_exact_warn' => 0,
        ];
        $warningCodes = [];
        $resolvedPublishableCount = 0;
        $fallbackUsedCount = 0;
        $retryableCount = 0;
        $retryScheduledCount = 0;
        $failureTrailCount = 0;
        $deliveredTrailCount = 0;

        foreach ($trailPayloads as $payload) {
            if (!is_array($payload)) {
                continue;
            }

            $status = trim((string) ($payload['deliveryStatus'] ?? 'pending'));
            if ('' !== $status) {
                $statusCounts[$status] = (int) ($statusCounts[$status] ?? 0) + 1;
            }

            $mode = trim((string) ($payload['mediaPolicyMode'] ?? 'strict_exact'));
            if ('' !== $mode) {
                $policyModeCounts[$mode] = (int) ($policyModeCounts[$mode] ?? 0) + 1;
            }

            if ((bool) ($payload['resolvedPublishable'] ?? false)) {
                ++$resolvedPublishableCount;
            }
            if ((bool) ($payload['fallbackUsed'] ?? false)) {
                ++$fallbackUsedCount;
            }
            if ((bool) ($payload['retryable'] ?? false)) {
                ++$retryableCount;
            }
            if ((bool) ($payload['retryScheduled'] ?? false)) {
                ++$retryScheduledCount;
            }
            if ((bool) ($payload['checks']['governanceTrailHasFailures'] ?? false)) {
                ++$failureTrailCount;
            }
            if ((bool) ($payload['checks']['governanceTrailHasDelivered'] ?? false)) {
                ++$deliveredTrailCount;
            }

            foreach (($payload['warnings'] ?? []) as $warning) {
                $warning = trim((string) $warning);
                if ('' !== $warning && !in_array($warning, $warningCodes, true)) {
                    $warningCodes[] = $warning;
                }
            }
        }

        sort($warningCodes);

        $totalTrails = count(array_filter($trailPayloads, 'is_array'));
        $checks = [
            'destinationGovernanceSummaryHasTrails' => $totalTrails > 0,
            'destinationGovernanceSummaryHasResolvedPublishable' => $resolvedPublishableCount > 0,
            'destinationGovernanceSummaryHasFallbackUsage' => $fallbackUsedCount > 0,
            'destinationGovernanceSummaryHasFailures' => $failureTrailCount > 0,
            'destinationGovernanceSummaryHasDelivered' => $deliveredTrailCount > 0,
            'destinationGovernanceSummaryHasRetryScheduled' => $retryScheduledCount > 0,
        ];

        return new CategorySyndicationDestinationGovernanceSummary(
            trim($destinationId),
            $totalTrails,
            $resolvedPublishableCount,
            $fallbackUsedCount,
            $retryableCount,
            $retryScheduledCount,
            $failureTrailCount,
            $deliveredTrailCount,
            $statusCounts,
            $policyModeCounts,
            $warningCodes,
            $checks,
        );
    }
}
