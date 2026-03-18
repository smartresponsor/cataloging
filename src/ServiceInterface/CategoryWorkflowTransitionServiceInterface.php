<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ServiceInterface;

use App\EventInterface\CategoryWorkflowTransitionedInterface;

interface CategoryWorkflowTransitionServiceInterface
{
    public function transition(string $categoryId, string $targetState, string $actorId, string $reason): CategoryWorkflowTransitionedInterface;
}
