<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CategorySyndicationCategoryGovernanceSummaryPolicyInterface;
use App\Cataloging\Service\CategoryPayloadValueNormalizer;
use App\Cataloging\ValueObject\CategorySyndicationCategoryGovernanceSummary;
use App\Cataloging\ValueObjectInterface\CategorySyndicationCategoryGovernanceSummaryInterface;

/**
 * Provides the category syndication category governance summary policy implementation.
 */
final class CategorySyndicationCategoryGovernanceSummaryPolicy implements CategorySyndicationCategoryGovernanceSummaryPolicyInterface
{
    /** @param list<array<string,mixed>> $trailPayloads */
    public function buildSummary(
        string $categoryId,
        array $trailPayloads,
    ): CategorySyndicationCategoryGovernanceSummaryInterface {
        $summary = CategorySyndicationGovernanceSummaryAccumulator::fromPayloads($trailPayloads);
        $destinationIds = [];
        foreach ($trailPayloads as $payload) {
            $destinationId = CategoryPayloadValueNormalizer::scalarString($payload['destinationId'] ?? null);
            if ('' !== $destinationId && !in_array($destinationId, $destinationIds, true)) {
                $destinationIds[] = $destinationId;
            }
        }

        sort($destinationIds);
        $totalTrails = $summary->totalTrails();

        $checks = [
            'categoryGovernanceSummaryHasTrails' => $totalTrails > 0,
            'categoryGovernanceSummaryHasDestinations' => [] !== $destinationIds,
            'categoryGovernanceSummaryHasResolvedPublishable' => $summary->resolvedPublishableCount() > 0,
            'categoryGovernanceSummaryHasFallbackUsage' => $summary->fallbackUsedCount() > 0,
            'categoryGovernanceSummaryHasFailures' => $summary->failureTrailCount() > 0,
            'categoryGovernanceSummaryHasDelivered' => $summary->deliveredTrailCount() > 0,
            'categoryGovernanceSummaryHasRetryScheduled' => $summary->retryScheduledCount() > 0,
        ];

        return new CategorySyndicationCategoryGovernanceSummary(
            trim($categoryId),
            $totalTrails,
            $summary->resolvedPublishableCount(),
            $summary->fallbackUsedCount(),
            $summary->retryableCount(),
            $summary->retryScheduledCount(),
            $summary->failureTrailCount(),
            $summary->deliveredTrailCount(),
            $destinationIds,
            $summary->statusCounts(),
            $summary->policyModeCounts(),
            $summary->warningCodes(),
            $checks,
        );
    }
}
