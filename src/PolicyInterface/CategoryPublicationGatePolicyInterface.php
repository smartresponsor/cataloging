<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryPublicationReadinessInterface;
use App\ValueObjectInterface\CategoryWorkflowStateInterface;

interface CategoryPublicationGatePolicyInterface
{
    public function canPublish(CategoryWorkflowStateInterface $workflowState, CategoryPublicationReadinessInterface $readiness, string $actorId, string $reason): bool;

    public function assertCanPublish(CategoryWorkflowStateInterface $workflowState, CategoryPublicationReadinessInterface $readiness, string $actorId, string $reason): void;
}
