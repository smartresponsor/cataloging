<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategoryMediaCoverageEvaluated;
use App\Cataloging\EventInterface\CategoryMediaCoverageEvaluatedInterface;
use App\Cataloging\PolicyInterface\CategoryMediaCoveragePolicyInterface;
use App\Cataloging\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogMediaCoverageServiceInterface;

final class CatalogMediaCoverageService implements CatalogMediaCoverageServiceInterface
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
