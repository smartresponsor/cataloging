<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategorySyndicationDestinationGovernanceSummaryBuilt;
use App\EventInterface\CategorySyndicationDestinationGovernanceSummaryBuiltInterface;
use App\PolicyInterface\CategorySyndicationDestinationGovernanceSummaryPolicyInterface;
use App\ServiceInterface\CatalogSyndicationDestinationGovernanceSummaryServiceInterface;

final class CatalogSyndicationDestinationGovernanceSummaryService implements CatalogSyndicationDestinationGovernanceSummaryServiceInterface
{
    public function __construct(
        private readonly CategorySyndicationDestinationGovernanceSummaryPolicyInterface $policy,
    ) {
    }

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
