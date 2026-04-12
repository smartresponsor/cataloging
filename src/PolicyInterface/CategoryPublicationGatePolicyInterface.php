<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryPublicationReadinessInterface;
use App\ValueObjectInterface\CategoryWorkflowStateInterface;

/**
 * Defines the contract for category publication gate policy.
 */
interface CategoryPublicationGatePolicyInterface
{
    /**
     * Determines whether the current workflow can publish.
     */
    public function canPublish(
        CategoryWorkflowStateInterface $workflowState,
        CategoryPublicationReadinessInterface $readiness,
        string $actorId,
        string $reason,
    ): bool;

    /**
     * Handles the assert can publish workflow.
     */
    public function assertCanPublish(
        CategoryWorkflowStateInterface $workflowState,
        CategoryPublicationReadinessInterface $readiness,
        string $actorId,
        string $reason,
    ): void;
}
