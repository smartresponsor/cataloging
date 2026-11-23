<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategoryWorkflowTransitionedInterface;

interface CatalogWorkflowTransitionServiceInterface
{
    public function transition(string $categoryId, string $targetState, string $actorId, string $reason): CategoryWorkflowTransitionedInterface;
}
