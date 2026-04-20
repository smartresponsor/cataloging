<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity;

use App\Cataloging\EntityInterface\CategoryWorkflowInterface;
use App\Cataloging\ValueObject\CategoryWorkflowState;
use App\Cataloging\ValueObjectInterface\CategoryWorkflowStateInterface;

/**
 * Represents the category workflow domain record.
 */
final readonly class CategoryWorkflow implements CategoryWorkflowInterface
{
    /**
     * Initializes the category workflow service collaborators.
     */
    public function __construct(
        private string $categoryId,
        private CategoryWorkflowState $state,
        private string $actorId,
        private string $reason,
        private \DateTimeImmutable $transitionedAt,
    ) {
    }

    public static function initialize(string $categoryId, string $actorId): self
    {
        return new self(
            $categoryId,
            CategoryWorkflowState::draft(),
            $actorId,
            'workflow initialized',
            new \DateTimeImmutable('now'),
        );
    }

    /**
     * Handles the transition to workflow.
     */
    public function transitionTo(CategoryWorkflowState $state, string $actorId, string $reason): self
    {
        return new self(
            $this->categoryId,
            $state,
            $actorId,
            $reason,
            new \DateTimeImmutable('now'),
        );
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the state workflow.
     */
    public function state(): CategoryWorkflowStateInterface
    {
        return $this->state;
    }

    /**
     * Handles the actor id workflow.
     */
    public function actorId(): string
    {
        return $this->actorId;
    }

    /**
     * Handles the reason workflow.
     */
    public function reason(): string
    {
        return $this->reason;
    }

    /**
     * Handles the transitioned at workflow.
     */
    public function transitionedAt(): \DateTimeImmutable
    {
        return $this->transitionedAt;
    }
}
