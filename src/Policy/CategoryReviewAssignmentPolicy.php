<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\EntityInterface\CategoryChangeRequestInterface;
use App\Cataloging\PolicyInterface\CategoryReviewAssignmentPolicyInterface;
use App\Cataloging\ValueObject\CategoryChangeRequestState;

/**
 * Provides the category review assignment policy implementation.
 */
final class CategoryReviewAssignmentPolicy implements CategoryReviewAssignmentPolicyInterface
{
    private const array ALLOWED_PRIORITIES = ['normal', 'high', 'urgent'];

    /**
     * Determines whether the current workflow can assign.
     */
    public function canAssign(
        CategoryChangeRequestInterface $request,
        string $assignedReviewer,
        string $assignedBy,
        string $priority,
    ): bool {
        if ('' === trim($assignedReviewer) || '' === trim($assignedBy)) {
            return false;
        }

        if (!in_array(trim($priority), self::ALLOWED_PRIORITIES, true)) {
            return false;
        }

        return in_array($request->state()->value(), [
            CategoryChangeRequestState::PROPOSED,
            CategoryChangeRequestState::IN_REVIEW,
        ], true);
    }

    /**
     * Handles the assert can assign workflow.
     */
    public function assertCanAssign(
        CategoryChangeRequestInterface $request,
        string $assignedReviewer,
        string $assignedBy,
        string $priority,
    ): void {
        if (!$this->canAssign($request, $assignedReviewer, $assignedBy, $priority)) {
            throw new \DomainException(sprintf('Category review assignment is not allowed for request state: %s', $request->state()->value()));
        }
    }
}
