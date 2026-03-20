<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

use App\Event\CategoryCompletenessEvaluated;
use App\EventInterface\CategoryCompletenessEvaluatedInterface;
use App\PolicyInterface\CategoryCompletenessPolicyInterface;
use App\ServiceInterface\CategoryMediaCompletenessBridgeServiceInterface;
use App\ServiceInterface\CategoryMediaCoverageServiceInterface;
use App\ValueObject\CategoryCompletenessReport;

final class CategoryMediaCompletenessBridgeService implements CategoryMediaCompletenessBridgeServiceInterface
{
    public function __construct(
        private readonly CategoryCompletenessPolicyInterface $completenessPolicy,
        private readonly CategoryMediaCoverageServiceInterface $mediaCoverageService,
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
