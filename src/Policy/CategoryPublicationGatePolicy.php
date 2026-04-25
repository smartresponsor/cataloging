<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CategoryPublicationGatePolicyInterface;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityState;
use App\Cataloging\ValueObjectInterface\CatalogCategoryWorkflowEntityStateInterface;
use App\Cataloging\ValueObjectInterface\CategoryPublicationReadinessInterface;

/**
 * Provides the category publication gate policy implementation.
 */
final class CategoryPublicationGatePolicy implements CategoryPublicationGatePolicyInterface
{
    /**
     * Determines whether the current workflow can publish.
     */
    public function canPublish(
        CatalogCategoryWorkflowEntityStateInterface $workflowState,
        CategoryPublicationReadinessInterface $readiness,
        string $actorId,
        string $reason,
    ): bool {
        if ('' === trim($actorId) || '' === trim($reason)) {
            return false;
        }

        if (!$workflowState->is(CatalogCategoryWorkflowEntityState::APPROVED)) {
            return false;
        }

        return $readiness->isPublishable();
    }

    /**
     * Handles the assert can publish workflow.
     */
    public function assertCanPublish(
        CatalogCategoryWorkflowEntityStateInterface $workflowState,
        CategoryPublicationReadinessInterface $readiness,
        string $actorId,
        string $reason,
    ): void {
        if ($this->canPublish($workflowState, $readiness, $actorId, $reason)) {
            return;
        }

        $details = [];
        if (!$workflowState->is(CatalogCategoryWorkflowEntityState::APPROVED)) {
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
