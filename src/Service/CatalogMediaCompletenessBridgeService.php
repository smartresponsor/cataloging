<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategoryCompletenessEvaluated;
use App\Cataloging\EventInterface\CategoryCompletenessEvaluatedInterface;
use App\Cataloging\PolicyInterface\CategoryCompletenessPolicyInterface;
use App\Cataloging\ServiceInterface\CatalogMediaCompletenessBridgeServiceInterface;
use App\Cataloging\ServiceInterface\CatalogMediaCoverageServiceInterface;
use App\Cataloging\ValueObject\CategoryCompletenessReport;
use App\Cataloging\ValueObject\CategoryEvaluationRequest;

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
        $mediaChecks = CategoryPayloadValueNormalizer::boolMap($mediaPayload['checks'] ?? null);
        $mergedChecks = array_merge($baseChecks, $mediaChecks);

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
