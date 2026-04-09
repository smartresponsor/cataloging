<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategorySyndicationCategoryGovernanceSummaryBuilt;
use App\EventInterface\CategorySyndicationCategoryGovernanceSummaryBuiltInterface;
use App\PolicyInterface\CategorySyndicationCategoryGovernanceSummaryPolicyInterface;
use App\ServiceInterface\CatalogSyndicationGovernanceSummaryServiceInterface;
/**
 * Provides the catalog syndication governance summary service application service.
 */
final class CatalogSyndicationGovernanceSummaryService implements CatalogSyndicationGovernanceSummaryServiceInterface
{
    /**
     * Initializes the catalog syndication governance summary service service collaborators.
     */
    public function __construct(
        private readonly CategorySyndicationCategoryGovernanceSummaryPolicyInterface $policy,
    ) {
    }

    /** @param list<array<string, mixed>> $trailPayloads */
    public function buildSummary(
        string $categoryId,
        array $trailPayloads,
        string $actorId,
        string $reason,
    ): CategorySyndicationCategoryGovernanceSummaryBuiltInterface
    {
        $summary = $this->policy->buildSummary($categoryId, $trailPayloads);

        return new CategorySyndicationCategoryGovernanceSummaryBuilt(
            [
                'categoryId' => $summary->categoryId(),
                'totalTrails' => $summary->totalTrails(),
                'resolvedPublishableCount' => $summary->resolvedPublishableCount(),
                'fallbackUsedCount' => $summary->fallbackUsedCount(),
                'retryableCount' => $summary->retryableCount(),
                'retryScheduledCount' => $summary->retryScheduledCount(),
                'failureTrailCount' => $summary->failureTrailCount(),
                'deliveredTrailCount' => $summary->deliveredTrailCount(),
                'destinationIds' => $summary->destinationIds(),
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
