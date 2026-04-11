<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryCompletenessEvaluated;
use App\EventInterface\CategoryCompletenessEvaluatedInterface;
use App\PolicyInterface\CategoryCompletenessPolicyInterface;
use App\ServiceInterface\CatalogMediaCompletenessBridgeServiceInterface;
use App\ServiceInterface\CatalogMediaCoverageServiceInterface;
use App\ValueObject\CategoryCompletenessReport;
use App\ValueObject\CategoryEvaluationRequest;

/**
 * Provides the catalog media completeness bridge service application service.
 */
final readonly class CatalogMediaCompletenessBridgeService implements CatalogMediaCompletenessBridgeServiceInterface
{
    /**
     * Initializes the catalog media completeness bridge service service collaborators.
     */
    public function __construct(
        private CategoryCompletenessPolicyInterface $completenessPolicy,
        private CatalogMediaCoverageServiceInterface $mediaCoverageService,
    ) {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(CategoryEvaluationRequest $request): CategoryCompletenessEvaluatedInterface
    {
        $baseChecks = $this->completenessPolicy->buildChecks($request->payload());
        $mediaPayload = $this->mediaCoverageService->evaluate($request)->payload();
        $mergedChecks = array_merge(
            $baseChecks,
            is_array($mediaPayload['checks'] ?? null) ? $mediaPayload['checks'] : [],
        );

        $report = CategoryCompletenessReport::fromChecks($mergedChecks);

        return new CategoryCompletenessEvaluated(
            trim($request->categoryId()),
            $report->score(),
            $report->isComplete(),
            $report->missingRequired(),
            $report->warnings(),
            $report->checks(),
            $report->publicationChecks(),
            trim($request->actorId()),
            trim($request->reason()),
            new \DateTimeImmutable('now'),
        );
    }
}
