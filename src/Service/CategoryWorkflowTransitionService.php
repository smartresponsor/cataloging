<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

use App\Entity\CategoryWorkflow;
use App\Event\CategoryWorkflowTransitioned;
use App\PolicyInterface\CategoryWorkflowPolicyInterface;
use App\RepositoryInterface\CategoryWorkflowRepositoryInterface;
use App\ServiceInterface\CategoryWorkflowTransitionServiceInterface;
use App\ValueObject\CategoryWorkflowState;

final class CategoryWorkflowTransitionService implements CategoryWorkflowTransitionServiceInterface
{
    public function __construct(
        private readonly CategoryWorkflowRepositoryInterface $repository,
        private readonly CategoryWorkflowPolicyInterface $policy,
    ) {
    }

    public function transition(string $categoryId, string $targetState, string $actorId, string $reason): CategoryWorkflowTransitioned
    {
        $current = $this->repository->findByCategoryId($categoryId);
        $currentWorkflow = $current instanceof CategoryWorkflow ? $current : CategoryWorkflow::initialize($categoryId, $actorId);
        $toState = CategoryWorkflowState::fromString($targetState);

        $this->policy->assertTransitionAllowed($currentWorkflow->state(), $toState, $actorId, $reason);

        $updated = $currentWorkflow->transitionTo($toState, $actorId, $reason);
        $this->repository->save($updated);

        $event = new CategoryWorkflowTransitioned(
            $categoryId,
            $currentWorkflow->state()->value(),
            $toState->value(),
            $actorId,
            $reason,
            $updated->transitionedAt(),
        );

        $this->repository->appendHistory($event);

        return $event;
    }
}
