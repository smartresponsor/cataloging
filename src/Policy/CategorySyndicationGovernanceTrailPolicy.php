<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CategorySyndicationGovernanceTrailPolicyInterface;
use App\Cataloging\Service\CategoryPayloadValueNormalizer;
use App\Cataloging\ValueObject\CategorySyndicationGovernanceTrailReport;
use App\Cataloging\ValueObjectInterface\CategorySyndicationGovernanceTrailReportInterface;

/**
 * Provides the category syndication governance trail policy implementation.
 */
/** @noinspection DuplicatedCode */
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
    ): CategorySyndicationGovernanceTrailReportInterface {
        $mediaPolicyMode = CategoryPayloadValueNormalizer::scalarString($policyAwarePayload['mediaPolicyMode'] ?? 'strict_exact');
        $strictPublishable = (bool) ($policyAwarePayload['strictPublishable'] ?? false);
        $fallbackPublishable = (bool) ($policyAwarePayload['fallbackPublishable'] ?? false);
        $resolvedPublishable = (bool) ($policyAwarePayload['resolvedPublishable'] ?? false);
        $fallbackUsed = (bool) ($policyAwarePayload['fallbackUsed'] ?? false);
        $deliveryStatus = CategoryPayloadValueNormalizer::scalarString($deliveryPayload['status'] ?? 'pending');
        $retryable = (bool) ($deliveryPayload['retryable'] ?? false);
        $retryScheduled = 'retry_scheduled' === $deliveryStatus
            || (CategoryPayloadValueNormalizer::intValue($recoveryPayload['scheduledRetries'] ?? null) > 0);

        $historyCounts = [
            'totalRecords' => CategoryPayloadValueNormalizer::intValue($historyPayload['totalRecords'] ?? null),
            'deliveredCount' => CategoryPayloadValueNormalizer::intValue($historyPayload['deliveredCount'] ?? null),
            'failedCount' => CategoryPayloadValueNormalizer::intValue($historyPayload['failedCount'] ?? null),
            'pendingCount' => CategoryPayloadValueNormalizer::intValue($historyPayload['pendingCount'] ?? null),
            'retryScheduledCount' => CategoryPayloadValueNormalizer::intValue($historyPayload['retryScheduledCount'] ?? null),
            'skippedCount' => CategoryPayloadValueNormalizer::intValue($historyPayload['skippedCount'] ?? null),
        ];

        $warnings = [];
        foreach ([$policyAwarePayload['warnings'] ?? null, $deliveryPayload['warnings'] ?? null] as $warningList) {
            foreach (CategoryPayloadValueNormalizer::stringList($warningList) as $warning) {
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
            CategoryPayloadValueNormalizer::scalarString(
                $policyAwarePayload['destinationId']
                ?? $deliveryPayload['destinationId']
                ?? $historyPayload['destinationId']
                ?? null,
            ),
            CategoryPayloadValueNormalizer::scalarString(
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
}
