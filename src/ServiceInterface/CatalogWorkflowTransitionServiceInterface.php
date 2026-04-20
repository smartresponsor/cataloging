<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategoryWorkflowTransitionedInterface;
use App\Cataloging\ValueObject\CategoryWorkflowTransitionRequest;

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
