<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryPublicationGateEvaluated;
use App\EventInterface\CategoryPublicationGateEvaluatedInterface;
use App\PolicyInterface\CategoryPublicationGatePolicyInterface;
use App\ServiceInterface\CatalogPublicationGateServiceInterface;
use App\ValueObject\CategoryPublicationGateAssertionRequest;
use App\ValueObject\CategoryPublicationGateEvaluationRequest;
use App\ValueObject\CategoryPublicationReadiness;
use App\ValueObject\CatalogCategoryWorkflowState;

/**
 * Provides the catalog publication gate service application service.
 */
final readonly class CatalogPublicationGateService implements CatalogPublicationGateServiceInterface
{
    /**
     * Initializes the catalog publication gate service service collaborators.
     */
    public function __construct(private CategoryPublicationGatePolicyInterface $policy)
    {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(CategoryPublicationGateEvaluationRequest $request): CategoryPublicationGateEvaluatedInterface
    {
        $state = CatalogCategoryWorkflowState::fromString($request->workflowState());
        $readiness = CategoryPublicationReadiness::fromChecks($request->checks());

        return new CategoryPublicationGateEvaluated(
            $request->categoryId(),
            $state->value(),
            $this->policy->canPublish($state, $readiness, $request->actorId(), $request->reason()),
            $readiness->blockers(),
            $readiness->warnings(),
            $readiness->checks(),
            trim($request->actorId()),
            trim($request->reason()),
            new \DateTimeImmutable('now'),
        );
    }

    /**
     * Handles the assert can publish workflow.
     */
    public function assertCanPublish(CategoryPublicationGateAssertionRequest $request): void
    {
        $state = CatalogCategoryWorkflowState::fromString($request->workflowState());
        $readiness = CategoryPublicationReadiness::fromChecks($request->checks());

        $this->policy->assertCanPublish($state, $readiness, $request->actorId(), $request->reason());
    }
}
