<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryWorkflowStateInterface;
/**
 * Defines the contract for category workflow policy.
 */
interface CategoryWorkflowPolicyInterface
{
    /**
     * Determines whether the current workflow can transition.
     */
    public function canTransition(
        CategoryWorkflowStateInterface $from,
        CategoryWorkflowStateInterface $to,
        string $actorId,
        string $reason,
    ): bool;
    /**
     * Handles the assert transition allowed workflow.
     */
    public function assertTransitionAllowed(
        CategoryWorkflowStateInterface $from,
        CategoryWorkflowStateInterface $to,
        string $actorId,
        string $reason,
    ): void;
}
