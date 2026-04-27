<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\Catalog\CatalogCategorySyndicationCategoryGovernanceSummaryBuiltEvent;
use App\Cataloging\EventInterface\Catalog\CatalogCategorySyndicationCategoryGovernanceSummaryBuiltEventInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationCategoryGovernanceSummaryPolicyInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationGovernanceSummaryServiceInterface;
use App\Cataloging\ValueObject\CategorySyndicationGovernanceSummaryRequest;

/**
 * Provides the catalog syndication governance summary service application service.
 */
final readonly class CatalogSyndicationGovernanceSummaryService implements CatalogSyndicationGovernanceSummaryServiceInterface
{
    /**
     * Initializes the catalog syndication governance summary service service collaborators.
     */
    public function __construct(
        private CategorySyndicationCategoryGovernanceSummaryPolicyInterface $policy,
    ) {
    }

    public function buildSummary(
        CategorySyndicationGovernanceSummaryRequest $request,
    ): CatalogCategorySyndicationCategoryGovernanceSummaryBuiltEventInterface {
        $summary = $this->policy->buildSummary($request->categoryId(), $request->trailPayloads());

        return new CatalogCategorySyndicationCategoryGovernanceSummaryBuiltEvent(
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
                'actorId' => trim($request->actorId()),
                'reason' => trim($request->reason()),
            ],
            new \DateTimeImmutable(),
        );
    }
}
