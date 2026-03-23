<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryPublicationGateEvaluatedInterface;

interface CategoryPublicationGateServiceInterface
{
    /** @param array<string,bool> $checks */
    public function evaluate(string $categoryId, string $workflowState, array $checks, string $actorId, string $reason): CategoryPublicationGateEvaluatedInterface;

    /** @param array<string,bool> $checks */
    public function assertCanPublish(string $workflowState, array $checks, string $actorId, string $reason): void;
}
