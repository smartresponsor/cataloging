<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategoryPublicationGateEvaluated;
use App\Cataloging\EventInterface\CategoryPublicationGateEvaluatedInterface;
use App\Cataloging\PolicyInterface\CategoryPublicationGatePolicyInterface;
use App\Cataloging\ServiceInterface\CatalogPublicationGateServiceInterface;
use App\Cataloging\ValueObject\CategoryPublicationReadiness;
use App\Cataloging\ValueObject\CategoryWorkflowState;

final class CatalogPublicationGateService implements CatalogPublicationGateServiceInterface
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
