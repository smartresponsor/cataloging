<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObjectInterface\CatalogCategoryWorkflowEntityStateInterface;
use App\Cataloging\ValueObjectInterface\CategoryPublicationReadinessInterface;

/**
 * Defines the contract for category publication gate policy.
 */
interface CategoryPublicationGatePolicyInterface
{
    /**
     * Determines whether the current workflow can publish.
     */
    public function canPublish(
        CatalogCategoryWorkflowEntityStateInterface $workflowState,
        CategoryPublicationReadinessInterface $readiness,
        string $actorId,
        string $reason,
    ): bool;

    /**
     * Handles the assert can publish workflow.
     */
    public function assertCanPublish(
        CatalogCategoryWorkflowEntityStateInterface $workflowState,
        CategoryPublicationReadinessInterface $readiness,
        string $actorId,
        string $reason,
    ): void;
}
