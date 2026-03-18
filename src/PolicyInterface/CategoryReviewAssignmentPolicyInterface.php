<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\PolicyInterface;

use App\EntityInterface\CategoryChangeRequestInterface;

interface CategoryReviewAssignmentPolicyInterface
{
    public function canAssign(CategoryChangeRequestInterface $request, string $assignedReviewer, string $assignedBy, string $priority): bool;

    public function assertCanAssign(CategoryChangeRequestInterface $request, string $assignedReviewer, string $assignedBy, string $priority): void;
}
