<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryPublicationGatePolicyInterface;
use App\ValueObject\CategoryWorkflowState;
use App\ValueObjectInterface\CategoryPublicationReadinessInterface;
use App\ValueObjectInterface\CategoryWorkflowStateInterface;
/**
 * Provides the category publication gate policy implementation.
 */
final class CategoryPublicationGatePolicy implements CategoryPublicationGatePolicyInterface
{
    /**
     * Determines whether the current workflow can publish.
     */
    public function canPublish(CategoryWorkflowStateInterface $workflowState, CategoryPublicationReadinessInterface $readiness, string $actorId, string $reason): bool
    {
        if ('' === trim($actorId) || '' === trim($reason)) {
            return false;
        }

        if (!$workflowState->is(CategoryWorkflowState::APPROVED)) {
            return false;
        }

        return $readiness->isPublishable();
    }
    /**
     * Handles the assert can publish workflow.
     */
    public function assertCanPublish(CategoryWorkflowStateInterface $workflowState, CategoryPublicationReadinessInterface $readiness, string $actorId, string $reason): void
    {
        if ($this->canPublish($workflowState, $readiness, $actorId, $reason)) {
            return;
        }

        $details = [];
        if (!$workflowState->is(CategoryWorkflowState::APPROVED)) {
            $details[] = 'workflowState='.$workflowState->value();
        }
        if ([] !== $readiness->blockers()) {
            $details[] = 'blockers='.implode(',', $readiness->blockers());
        }
        if ('' === trim($actorId)) {
            $details[] = 'actorId missing';
        }
        if ('' === trim($reason)) {
            $details[] = 'reason missing';
        }

        throw new \DomainException('Category publication gate failed: '.implode('; ', $details));
    }
}
