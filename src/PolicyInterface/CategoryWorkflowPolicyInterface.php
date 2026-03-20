<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryWorkflowStateInterface;

interface CategoryWorkflowPolicyInterface
{
    public function canTransition(CategoryWorkflowStateInterface $from, CategoryWorkflowStateInterface $to, string $actorId, string $reason): bool;

    public function assertTransitionAllowed(CategoryWorkflowStateInterface $from, CategoryWorkflowStateInterface $to, string $actorId, string $reason): void;
}
