<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryChangeRequestAssignedInterface;
use App\ValueObject\CategoryReviewAssignmentRequest;

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
