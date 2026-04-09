<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryCompletenessEvaluated;
use App\EventInterface\CategoryCompletenessEvaluatedInterface;
use App\PolicyInterface\CategoryCompletenessPolicyInterface;
use App\ServiceInterface\CatalogCompletenessServiceInterface;
use App\ValueObject\CategoryCompletenessReport;
/**
 * Provides the catalog completeness service application service.
 */
final class CatalogCompletenessService implements CatalogCompletenessServiceInterface
{
    /**
     * Initializes the catalog completeness service service collaborators.
     */
    public function __construct(private readonly CategoryCompletenessPolicyInterface $policy)
    {
    }
    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        string $categoryId,
        array $payload,
        string $actorId,
        string $reason,
    ): CategoryCompletenessEvaluatedInterface
    {
        $report = CategoryCompletenessReport::fromChecks($this->policy->buildChecks($payload));

        return new CategoryCompletenessEvaluated(
            trim($categoryId),
            $report->score(),
            $report->isComplete(),
            $report->missingRequired(),
            $report->warnings(),
            $report->checks(),
            $report->publicationChecks(),
            trim($actorId),
            trim($reason),
            new \DateTimeImmutable('now'),
        );
    }
}
