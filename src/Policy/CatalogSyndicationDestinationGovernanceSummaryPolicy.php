<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CatalogSyndicationDestinationGovernanceSummaryPolicyInterface;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationGovernanceSummary;
use App\Cataloging\ValueObjectInterface\CatalogSyndicationDestinationGovernanceSummaryInterface;

/**
 * Provides the category syndication destination governance summary policy implementation.
 */
final class CatalogSyndicationDestinationGovernanceSummaryPolicy implements CatalogSyndicationDestinationGovernanceSummaryPolicyInterface
{
    /** @param list<array<string,mixed>> $trailPayloads */
    public function buildSummary(
        string $destinationId,
        array $trailPayloads,
    ): CatalogSyndicationDestinationGovernanceSummaryInterface {
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

        return new CatalogSyndicationDestinationGovernanceSummary(
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
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationGovernanceSummaryPolicy', false)) {
    class_alias(CatalogSyndicationDestinationGovernanceSummaryPolicy::class, __NAMESPACE__.'\\SyndicationDestinationGovernanceSummaryPolicy');
}
