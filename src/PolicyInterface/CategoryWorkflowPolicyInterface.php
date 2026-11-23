<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObjectInterface\CategoryWorkflowStateInterface;

interface CategoryWorkflowPolicyInterface
{
    public function canTransition(CategoryWorkflowStateInterface $from, CategoryWorkflowStateInterface $to, string $actorId, string $reason): bool;

    public function assertTransitionAllowed(CategoryWorkflowStateInterface $from, CategoryWorkflowStateInterface $to, string $actorId, string $reason): void;
}
