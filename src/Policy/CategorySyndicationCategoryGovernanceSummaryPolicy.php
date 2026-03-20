<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationCategoryGovernanceSummaryPolicyInterface;
use App\ValueObject\CategorySyndicationCategoryGovernanceSummary;
use App\ValueObjectInterface\CategorySyndicationCategoryGovernanceSummaryInterface;

final class CategorySyndicationCategoryGovernanceSummaryPolicy implements CategorySyndicationCategoryGovernanceSummaryPolicyInterface
{
    public function buildSummary(string $categoryId, array $trailPayloads): CategorySyndicationCategoryGovernanceSummaryInterface
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
        $destinationIds = [];
        $warningCodes = [];
        $resolvedPublishableCount = 0;
        $fallbackUsedCount = 0;
        $retryableCount = 0;
        $retryScheduledCount = 0;
        $failureTrailCount = 0;
        $deliveredTrailCount = 0;
        $totalTrails = 0;

        foreach ($trailPayloads as $payload) {
            if (!is_array($payload)) {
                continue;
            }
            ++$totalTrails;

            $destinationId = trim((string) ($payload['destinationId'] ?? ''));
            if ('' !== $destinationId && !in_array($destinationId, $destinationIds, true)) {
                $destinationIds[] = $destinationId;
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

        sort($destinationIds);
        sort($warningCodes);

        $checks = [
            'categoryGovernanceSummaryHasTrails' => $totalTrails > 0,
            'categoryGovernanceSummaryHasDestinations' => [] !== $destinationIds,
            'categoryGovernanceSummaryHasResolvedPublishable' => $resolvedPublishableCount > 0,
            'categoryGovernanceSummaryHasFallbackUsage' => $fallbackUsedCount > 0,
            'categoryGovernanceSummaryHasFailures' => $failureTrailCount > 0,
            'categoryGovernanceSummaryHasDelivered' => $deliveredTrailCount > 0,
            'categoryGovernanceSummaryHasRetryScheduled' => $retryScheduledCount > 0,
        ];

        return new CategorySyndicationCategoryGovernanceSummary(
            trim($categoryId),
            $totalTrails,
            $resolvedPublishableCount,
            $fallbackUsedCount,
            $retryableCount,
            $retryScheduledCount,
            $failureTrailCount,
            $deliveredTrailCount,
            $destinationIds,
            $statusCounts,
            $policyModeCounts,
            $warningCodes,
            $checks,
        );
    }
}
