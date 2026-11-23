<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObjectInterface\CategoryPublicationReadinessInterface;
use App\Cataloging\ValueObjectInterface\CategoryWorkflowStateInterface;

interface CategoryPublicationGatePolicyInterface
{
    public function canPublish(CategoryWorkflowStateInterface $workflowState, CategoryPublicationReadinessInterface $readiness, string $actorId, string $reason): bool;

    public function assertCanPublish(CategoryWorkflowStateInterface $workflowState, CategoryPublicationReadinessInterface $readiness, string $actorId, string $reason): void;
}
