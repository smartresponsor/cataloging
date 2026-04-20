<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EventInterface;

/**
 * Defines the contract for category workflow transitioned.
 */
interface CategoryWorkflowTransitionedInterface
{
    /**
     * Handles the event name workflow.
     */
    public function eventName(): string;

    /** @return array<string,mixed> */
    public function payload(): array;
}
