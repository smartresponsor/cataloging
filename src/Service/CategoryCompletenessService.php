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
use App\ServiceInterface\CategoryCompletenessServiceInterface;
use App\ValueObject\CategoryCompletenessReport;

final class CategoryCompletenessService implements CategoryCompletenessServiceInterface
{
    public function __construct(private readonly CategoryCompletenessPolicyInterface $policy)
    {
    }

    public function evaluate(string $categoryId, array $payload, string $actorId, string $reason): CategoryCompletenessEvaluatedInterface
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
