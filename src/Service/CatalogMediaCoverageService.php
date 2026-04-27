<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategoryMediaCoverageEvaluated;
use App\Cataloging\EventInterface\CategoryMediaCoverageEvaluatedInterface;
use App\Cataloging\PolicyInterface\CategoryMediaCoveragePolicyInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryMediaBindingRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogMediaCoverageServiceInterface;
use App\Cataloging\ValueObject\CategoryEvaluationRequest;

/**
 * Provides the catalog media coverage service application service.
 */
final readonly class CatalogMediaCoverageService implements CatalogMediaCoverageServiceInterface
{
    /**
     * Initializes the catalog media coverage service service collaborators.
     */
    public function __construct(
        private CatalogCategoryMediaBindingRepositoryInterface $repository,
        private CategoryMediaCoveragePolicyInterface $policy,
    ) {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(CategoryEvaluationRequest $request): CategoryMediaCoverageEvaluatedInterface
    {
        $report = $this->policy->buildReport(
            $request->payload(),
            $this->repository->bindingsForCategory($request->categoryId()),
        );

        return new CategoryMediaCoverageEvaluated(
            trim($request->categoryId()),
            $report->requiredMissing(),
            $report->warnings(),
            $report->checks(),
            trim($request->actorId()),
            trim($request->reason()),
            new \DateTimeImmutable('now'),
        );
    }
}
