<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategoryChangeRequestAssignedInterface;
use App\Cataloging\ValueObject\CategoryReviewAssignmentRequest;

/**
 * Defines the contract for catalog review assignment service.
 */
interface CatalogReviewAssignmentServiceInterface
{
    /**
     * Handles the assign workflow.
     */
    public function assign(CategoryReviewAssignmentRequest $request): CategoryChangeRequestAssignedInterface;
}
