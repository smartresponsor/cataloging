<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CategorySyndicationDestinationGovernanceSummaryPolicyInterface;
use App\Cataloging\ValueObject\CategorySyndicationDestinationGovernanceSummary;
use App\Cataloging\ValueObjectInterface\CategorySyndicationDestinationGovernanceSummaryInterface;

/**
 * Provides the category syndication destination governance summary policy implementation.
 */
final class CategorySyndicationDestinationGovernanceSummaryPolicy implements CategorySyndicationDestinationGovernanceSummaryPolicyInterface
{
    /** @param list<array<string,mixed>> $trailPayloads */
    public function buildSummary(
        string $destinationId,
        array $trailPayloads,
    ): CategorySyndicationDestinationGovernanceSummaryInterface {
        $summary = CategorySyndicationGovernanceSummaryAccumulator::fromPayloads($trailPayloads);
        $totalTrails = $summary->totalTrails();
        $checks = [
            'destinationGovernanceSummaryHasTrails' => $totalTrails > 0,
            'destinationGovernanceSummaryHasResolvedPublishable' => $summary->resolvedPublishableCount() > 0,
            'destinationGovernanceSummaryHasFallbackUsage' => $summary->fallbackUsedCount() > 0,
            'destinationGovernanceSummaryHasFailures' => $summary->failureTrailCount() > 0,
            'destinationGovernanceSummaryHasDelivered' => $summary->deliveredTrailCount() > 0,
            'destinationGovernanceSummaryHasRetryScheduled' => $summary->retryScheduledCount() > 0,
        ];

        return new CategorySyndicationDestinationGovernanceSummary(
            trim($destinationId),
            $totalTrails,
            $summary->resolvedPublishableCount(),
            $summary->fallbackUsedCount(),
            $summary->retryableCount(),
            $summary->retryScheduledCount(),
            $summary->failureTrailCount(),
            $summary->deliveredTrailCount(),
            $summary->statusCounts(),
            $summary->policyModeCounts(),
            $summary->warningCodes(),
            $checks,
        );
    }
}
