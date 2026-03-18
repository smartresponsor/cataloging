<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use App\Event\CategoryMediaCoverageEvaluated;
use App\EventInterface\CategoryMediaCoverageEvaluatedInterface;
use App\PolicyInterface\CategoryMediaCoveragePolicyInterface;
use App\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\ServiceInterface\CategoryMediaCoverageServiceInterface;

final class CategoryMediaCoverageService implements CategoryMediaCoverageServiceInterface
{
    public function __construct(
        private readonly CategoryMediaBindingRepositoryInterface $repository,
        private readonly CategoryMediaCoveragePolicyInterface $policy,
    ) {
    }

    public function evaluate(string $categoryId, array $payload, string $actorId, string $reason): CategoryMediaCoverageEvaluatedInterface
    {
        $report = $this->policy->buildReport($payload, $this->repository->bindingsForCategory($categoryId));

        return new CategoryMediaCoverageEvaluated(
            trim($categoryId),
            $report->requiredMissing(),
            $report->warnings(),
            $report->checks(),
            trim($actorId),
            trim($reason),
            new \DateTimeImmutable('now'),
        );
    }
}
