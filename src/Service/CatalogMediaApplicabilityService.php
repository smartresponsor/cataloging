<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategoryMediaApplicabilityEvaluated;
use App\Cataloging\EventInterface\CategoryMediaApplicabilityEvaluatedInterface;
use App\Cataloging\PolicyInterface\CategoryMediaApplicabilityPolicyInterface;
use App\Cataloging\RepositoryInterface\CatalogCategoryMediaBindingEntityRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogMediaApplicabilityServiceInterface;
use App\Cataloging\ValueObject\CategoryEvaluationRequest;

/**
 * Provides the catalog media applicability service application service.
 */
final readonly class CatalogMediaApplicabilityService implements CatalogMediaApplicabilityServiceInterface
{
    /**
     * Initializes the catalog media applicability service service collaborators.
     */
    public function __construct(
        private CatalogCategoryMediaBindingEntityRepositoryInterface $repository,
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
