<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategoryPublicationGateEvaluated;
use App\Cataloging\EventInterface\CategoryPublicationGateEvaluatedInterface;
use App\Cataloging\PolicyInterface\CategoryPublicationGatePolicyInterface;
use App\Cataloging\ServiceInterface\CatalogPublicationGateServiceInterface;
use App\Cataloging\ValueObject\CategoryPublicationGateAssertionRequest;
use App\Cataloging\ValueObject\CategoryPublicationGateEvaluationRequest;
use App\Cataloging\ValueObject\CategoryPublicationReadiness;
use App\Cataloging\ValueObject\CategoryWorkflowState;

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
        $state = CategoryWorkflowState::fromString($request->workflowState());
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
        $state = CategoryWorkflowState::fromString($request->workflowState());
        $readiness = CategoryPublicationReadiness::fromChecks($request->checks());

        $this->policy->assertCanPublish($state, $readiness, $request->actorId(), $request->reason());
    }
}
