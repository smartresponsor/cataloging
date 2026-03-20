<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

use App\Event\CategoryPublicationGateEvaluated;
use App\EventInterface\CategoryPublicationGateEvaluatedInterface;
use App\PolicyInterface\CategoryPublicationGatePolicyInterface;
use App\ServiceInterface\CategoryPublicationGateServiceInterface;
use App\ValueObject\CategoryPublicationReadiness;
use App\ValueObject\CategoryWorkflowState;

final class CategoryPublicationGateService implements CategoryPublicationGateServiceInterface
{
    public function __construct(private readonly CategoryPublicationGatePolicyInterface $policy)
    {
    }

    public function evaluate(string $categoryId, string $workflowState, array $checks, string $actorId, string $reason): CategoryPublicationGateEvaluatedInterface
    {
        $state = CategoryWorkflowState::fromString($workflowState);
        $readiness = CategoryPublicationReadiness::fromChecks($checks);

        return new CategoryPublicationGateEvaluated(
            $categoryId,
            $state->value(),
            $this->policy->canPublish($state, $readiness, $actorId, $reason),
            $readiness->blockers(),
            $readiness->warnings(),
            $readiness->checks(),
            trim($actorId),
            trim($reason),
            new \DateTimeImmutable('now'),
        );
    }

    public function assertCanPublish(string $workflowState, array $checks, string $actorId, string $reason): void
    {
        $state = CategoryWorkflowState::fromString($workflowState);
        $readiness = CategoryPublicationReadiness::fromChecks($checks);

        $this->policy->assertCanPublish($state, $readiness, $actorId, $reason);
    }
}
