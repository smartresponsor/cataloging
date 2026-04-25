<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\EntityInterface\CategoryChangeRequestInterface;

/**
 * Defines the contract for category review assignment policy.
 */
interface CatalogCategoryReviewAssignmentEntityPolicyInterface
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
if (!class_exists(__NAMESPACE__.'\\CategoryReviewAssignmentPolicyInterface', false)) {
    class_alias(CatalogCategoryReviewAssignmentEntityPolicyInterface::class, __NAMESPACE__.'\\CategoryReviewAssignmentPolicyInterface');
}
