<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObjectInterface\CatalogCategoryWorkflowEntityStateInterface;

/**
 * Defines the contract for category workflow policy.
 */
interface CatalogCategoryWorkflowEntityPolicyInterface
{
    /**
     * Determines whether the current workflow can transition.
     */
    public function canTransition(
        CatalogCategoryWorkflowEntityStateInterface $from,
        CatalogCategoryWorkflowEntityStateInterface $to,
        string $actorId,
        string $reason,
    ): bool;

    /**
     * Handles the assert transition allowed workflow.
     */
    public function assertTransitionAllowed(
        CatalogCategoryWorkflowEntityStateInterface $from,
        CatalogCategoryWorkflowEntityStateInterface $to,
        string $actorId,
        string $reason,
    ): void;
}
