<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CatalogCategoryWorkflowStateInterface;

/**
 * Defines the contract for category workflow policy.
 */
interface CategoryWorkflowPolicyInterface
{
    /**
     * Determines whether the current workflow can transition.
     */
    public function canTransition(
        CatalogCategoryWorkflowStateInterface $from,
        CatalogCategoryWorkflowStateInterface $to,
        string $actorId,
        string $reason,
    ): bool;

    /**
     * Handles the assert transition allowed workflow.
     */
    public function assertTransitionAllowed(
        CatalogCategoryWorkflowStateInterface $from,
        CatalogCategoryWorkflowStateInterface $to,
        string $actorId,
        string $reason,
    ): void;
}
