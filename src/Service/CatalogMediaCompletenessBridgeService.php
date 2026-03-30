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

final class CatalogMediaCompletenessBridgeService implements CatalogMediaCompletenessBridgeServiceInterface
{
    public function __construct(
        private readonly CategoryCompletenessPolicyInterface $completenessPolicy,
        private readonly CatalogMediaCoverageServiceInterface $mediaCoverageService,
    ) {
    }

    public function evaluate(string $categoryId, array $payload, string $actorId, string $reason): CategoryCompletenessEvaluatedInterface
    {
        $baseChecks = $this->completenessPolicy->buildChecks($payload);
        $mediaPayload = $this->mediaCoverageService->evaluate($categoryId, $payload, $actorId, $reason)->payload();
        $mergedChecks = array_merge($baseChecks, is_array($mediaPayload['checks'] ?? null) ? $mediaPayload['checks'] : []);

        $report = CategoryCompletenessReport::fromChecks($mergedChecks);

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
