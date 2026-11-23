<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategorySyndicationDestinationGovernanceSummaryBuilt;
use App\Cataloging\EventInterface\CategorySyndicationDestinationGovernanceSummaryBuiltInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationDestinationGovernanceSummaryPolicyInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationDestinationGovernanceSummaryServiceInterface;

final class CatalogSyndicationDestinationGovernanceSummaryService implements CatalogSyndicationDestinationGovernanceSummaryServiceInterface
{
    public function __construct(
        private readonly CategorySyndicationDestinationGovernanceSummaryPolicyInterface $policy,
    ) {
    }

    /** @param list<array<string, mixed>> $trailPayloads */
    public function buildSummary(string $destinationId, array $trailPayloads, string $actorId, string $reason): CategorySyndicationDestinationGovernanceSummaryBuiltInterface
    {
        $summary = $this->policy->buildSummary($destinationId, $trailPayloads);

        return new CategorySyndicationDestinationGovernanceSummaryBuilt(
            [
                'destinationId' => $summary->destinationId(),
                'totalTrails' => $summary->totalTrails(),
                'resolvedPublishableCount' => $summary->resolvedPublishableCount(),
                'fallbackUsedCount' => $summary->fallbackUsedCount(),
                'retryableCount' => $summary->retryableCount(),
                'retryScheduledCount' => $summary->retryScheduledCount(),
                'failureTrailCount' => $summary->failureTrailCount(),
                'deliveredTrailCount' => $summary->deliveredTrailCount(),
                'statusCounts' => $summary->statusCounts(),
                'policyModeCounts' => $summary->policyModeCounts(),
                'warningCodes' => $summary->warningCodes(),
                'checks' => $summary->checks(),
                'actorId' => trim($actorId),
                'reason' => trim($reason),
            ],
            new \DateTimeImmutable(),
        );
    }
}
