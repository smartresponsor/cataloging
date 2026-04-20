<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CategoryWorkflow;
use App\Cataloging\Event\CategoryWorkflowTransitioned;
use App\Cataloging\PolicyInterface\CategoryWorkflowPolicyInterface;
use App\Cataloging\RepositoryInterface\CategoryWorkflowRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogWorkflowTransitionServiceInterface;
use App\Cataloging\ValueObject\CategoryWorkflowState;
use App\Cataloging\ValueObject\CategoryWorkflowTransitionRequest;

/**
 * Provides the catalog workflow transition service application service.
 */
final readonly class CatalogWorkflowTransitionService implements CatalogWorkflowTransitionServiceInterface
{
    /**
     * Initializes the catalog workflow transition service service collaborators.
     */
    public function __construct(
        private CategoryWorkflowRepositoryInterface $repository,
        private CategoryWorkflowPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the transition workflow.
     */
    public function transition(CategoryWorkflowTransitionRequest $request): CategoryWorkflowTransitioned
    {
        $current = $this->repository->findByCategoryId($request->categoryId());
        $currentWorkflow = $current instanceof CategoryWorkflow
            ? $current
            : CategoryWorkflow::initialize($request->categoryId(), $request->actorId());
        $toState = CategoryWorkflowState::fromString($request->targetState());

        $this->policy->assertTransitionAllowed(
            $currentWorkflow->state(),
            $toState,
            $request->actorId(),
            $request->reason(),
        );

        $updated = $currentWorkflow->transitionTo($toState, $request->actorId(), $request->reason());
        $this->repository->save($updated);

        $event = new CategoryWorkflowTransitioned(
            $request->categoryId(),
            $currentWorkflow->state()->value(),
            $toState->value(),
            $request->actorId(),
            $request->reason(),
            $updated->transitionedAt(),
        );

        $this->repository->appendHistory($event);

        return $event;
    }
}
