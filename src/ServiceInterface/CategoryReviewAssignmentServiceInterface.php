<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ServiceInterface;

use App\EventInterface\CategoryChangeRequestAssignedInterface;

interface CategoryReviewAssignmentServiceInterface
{
    public function assign(
        string $requestId,
        string $assignedReviewer,
        string $assignedBy,
        string $priority = 'normal',
        ?\DateTimeImmutable $dueAt = null,
    ): CategoryChangeRequestAssignedInterface;
}
