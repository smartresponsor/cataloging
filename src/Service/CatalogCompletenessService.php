<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryCompletenessEvaluated;
use App\EventInterface\CategoryCompletenessEvaluatedInterface;
use App\PolicyInterface\CategoryCompletenessPolicyInterface;
use App\ServiceInterface\CatalogCompletenessServiceInterface;
use App\ValueObject\CategoryCompletenessReport;
use App\ValueObject\CategoryEvaluationRequest;

/**
 * Provides the catalog completeness service application service.
 */
final readonly class CatalogCompletenessService implements CatalogCompletenessServiceInterface
{
    /**
     * Initializes the catalog completeness service service collaborators.
     */
    public function __construct(private CategoryCompletenessPolicyInterface $policy)
    {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(CategoryEvaluationRequest $request): CategoryCompletenessEvaluatedInterface
    {
        $report = CategoryCompletenessReport::fromChecks($this->policy->buildChecks($request->payload()));

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
