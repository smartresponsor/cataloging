<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Policy;

use App\EntityInterface\CategoryChangeRequestInterface;
use App\PolicyInterface\CategoryReviewAssignmentPolicyInterface;
use App\ValueObject\CategoryChangeRequestState;

final class CategoryReviewAssignmentPolicy implements CategoryReviewAssignmentPolicyInterface
{
    private const ALLOWED_PRIORITIES = ['normal', 'high', 'urgent'];

    public function canAssign(CategoryChangeRequestInterface $request, string $assignedReviewer, string $assignedBy, string $priority): bool
    {
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

    public function assertCanAssign(CategoryChangeRequestInterface $request, string $assignedReviewer, string $assignedBy, string $priority): void
    {
        if (!$this->canAssign($request, $assignedReviewer, $assignedBy, $priority)) {
            throw new \DomainException(sprintf('Category review assignment is not allowed for request state: %s', $request->state()->value()));
        }
    }
}
