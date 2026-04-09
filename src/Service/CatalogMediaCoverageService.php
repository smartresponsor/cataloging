<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryMediaCoverageEvaluated;
use App\EventInterface\CategoryMediaCoverageEvaluatedInterface;
use App\PolicyInterface\CategoryMediaCoveragePolicyInterface;
use App\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\ServiceInterface\CatalogMediaCoverageServiceInterface;

/**
 * Provides the catalog media coverage service application service.
 */
final readonly class CatalogMediaCoverageService implements CatalogMediaCoverageServiceInterface
{
    /**
     * Initializes the catalog media coverage service service collaborators.
     */
    public function __construct(
        private CategoryMediaBindingRepositoryInterface $repository,
        private CategoryMediaCoveragePolicyInterface $policy,
    ) {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        string $categoryId,
        array $payload,
        string $actorId,
        string $reason,
    ): CategoryMediaCoverageEvaluatedInterface {
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
