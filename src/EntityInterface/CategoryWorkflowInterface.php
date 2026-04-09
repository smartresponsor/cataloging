<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EntityInterface;

use App\ValueObjectInterface\CategoryWorkflowStateInterface;

/**
 * Defines the contract for category workflow.
 */
interface CategoryWorkflowInterface
{
    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the state workflow.
     */
    public function state(): CategoryWorkflowStateInterface;

    /**
     * Handles the actor id workflow.
     */
    public function actorId(): string;

    /**
     * Handles the reason workflow.
     */
    public function reason(): string;

    /**
     * Handles the transitioned at workflow.
     */
    public function transitionedAt(): \DateTimeImmutable;
}
