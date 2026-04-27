<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogCategoryWorkflowEntity;
use App\Cataloging\Event\CatalogCategoryWorkflowEntityTransitioned;
use App\Cataloging\PolicyInterface\CatalogCategoryWorkflowEntityPolicyInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryWorkflowRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogWorkflowTransitionServiceInterface;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityState;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityTransitionRequest;

/**
 * Provides the catalog workflow transition service application service.
 */
final readonly class CatalogWorkflowTransitionService implements CatalogWorkflowTransitionServiceInterface
{
    /**
     * Initializes the catalog workflow transition service service collaborators.
     */
    public function __construct(
        private CatalogCategoryWorkflowRepositoryInterface $repository,
        private CatalogCategoryWorkflowEntityPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the transition workflow.
     */
    public function transition(CatalogCategoryWorkflowEntityTransitionRequest $request): CatalogCategoryWorkflowEntityTransitioned
    {
        $current = $this->repository->findByCategoryId($request->categoryId());
        $currentWorkflow = $current instanceof CatalogCategoryWorkflowEntity
            ? $current
            : CatalogCategoryWorkflowEntity::initialize($request->categoryId(), $request->actorId());
        $toState = CatalogCategoryWorkflowEntityState::fromString($request->targetState());

        $this->policy->assertTransitionAllowed(
            $currentWorkflow->state(),
            $toState,
            $request->actorId(),
            $request->reason(),
        );

        $updated = $currentWorkflow->transitionTo($toState, $request->actorId(), $request->reason());
        $this->repository->save($updated);

        $event = new CatalogCategoryWorkflowEntityTransitioned(
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
