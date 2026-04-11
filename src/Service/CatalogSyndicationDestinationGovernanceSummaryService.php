<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategorySyndicationDestinationGovernanceSummaryBuilt;
use App\EventInterface\CategorySyndicationDestinationGovernanceSummaryBuiltInterface;
use App\PolicyInterface\CategorySyndicationDestinationGovernanceSummaryPolicyInterface;
use App\ServiceInterface\CatalogSyndicationDestinationGovernanceSummaryServiceInterface;
use App\ValueObject\CategorySyndicationDestinationGovernanceSummaryRequest;

/**
 * Provides the catalog syndication destination governance summary service application service.
 */
final readonly class CatalogSyndicationDestinationGovernanceSummaryService implements CatalogSyndicationDestinationGovernanceSummaryServiceInterface
{
    /**
     * Initializes the catalog syndication destination governance summary service service collaborators.
     */
    public function __construct(
        private CategorySyndicationDestinationGovernanceSummaryPolicyInterface $policy,
    ) {
    }

    public function buildSummary(
        CategorySyndicationDestinationGovernanceSummaryRequest $request,
    ): CategorySyndicationDestinationGovernanceSummaryBuiltInterface {
        $summary = $this->policy->buildSummary($request->destinationId(), $request->trailPayloads());

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
                'actorId' => trim($request->actorId()),
                'reason' => trim($request->reason()),
            ],
            new \DateTimeImmutable(),
        );
    }
}
