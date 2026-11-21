<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EventInterface\Catalog;

/**
 * Defines the contract for category workflow transitioned.
 */
interface CatalogCategoryWorkflowEntityTransitionedEventInterface
{
    /**
     * Handles the event nameEntity workflow.
     */
    public function eventName(): string;

    /** @return array<string,mixed> */
    public function payload(): array;
}
if (!class_exists(__NAMESPACE__.'\\CategoryWorkflowTransitionedInterface', false)) {
    class_alias(CatalogCategoryWorkflowEntityTransitionedEventInterface::class, __NAMESPACE__.'\\CategoryWorkflowTransitionedInterface');
}
