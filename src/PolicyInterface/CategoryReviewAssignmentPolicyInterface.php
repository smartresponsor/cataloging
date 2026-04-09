<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\EntityInterface\CategoryChangeRequestInterface;
/**
 * Defines the contract for category review assignment policy.
 */
interface CategoryReviewAssignmentPolicyInterface
{
    /**
     * Determines whether the current workflow can assign.
     */
    public function canAssign(
        CategoryChangeRequestInterface $request,
        string $assignedReviewer,
        string $assignedBy,
        string $priority,
    ): bool;
    /**
     * Handles the assert can assign workflow.
     */
    public function assertCanAssign(
        CategoryChangeRequestInterface $request,
        string $assignedReviewer,
        string $assignedBy,
        string $priority,
    ): void;
}
