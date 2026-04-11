<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryMediaApplicabilityEvaluated;
use App\EventInterface\CategoryMediaApplicabilityEvaluatedInterface;
use App\PolicyInterface\CategoryMediaApplicabilityPolicyInterface;
use App\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\ServiceInterface\CatalogMediaApplicabilityServiceInterface;
use App\ValueObject\CategoryEvaluationRequest;

/**
 * Provides the catalog media applicability service application service.
 */
final readonly class CatalogMediaApplicabilityService implements CatalogMediaApplicabilityServiceInterface
{
    /**
     * Initializes the catalog media applicability service service collaborators.
     */
    public function __construct(
        private CategoryMediaBindingRepositoryInterface $repository,
        private CategoryMediaApplicabilityPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(CategoryEvaluationRequest $request): CategoryMediaApplicabilityEvaluatedInterface
    {
        $report = $this->policy->buildReport(
            $request->payload(),
            $this->repository->bindingsForCategory($request->categoryId()),
        );
        $payload = $request->payload();

        return new CategoryMediaApplicabilityEvaluated(
            trim($request->categoryId()),
            trim($this->scalarString($payload['channel'] ?? '')),
            trim($this->scalarString($payload['locale'] ?? '')),
            $report->requiredMissing(),
            $report->warnings(),
            $report->checks(),
            $report->matchedBindingIds(),
            trim($request->actorId()),
            trim($request->reason()),
            new \DateTimeImmutable('now'),
        );
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
