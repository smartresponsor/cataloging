<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryWorkflowTransitionedInterface;
use App\ValueObject\CategoryWorkflowTransitionRequest;

/**
 * Defines the contract for catalog workflow transition service.
 */
interface CatalogWorkflowTransitionServiceInterface
{
    /**
     * Handles the transition workflow.
     */
    public function transition(CategoryWorkflowTransitionRequest $request): CategoryWorkflowTransitionedInterface;
}
