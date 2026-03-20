<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Entity;

use App\EntityInterface\CategoryWorkflowInterface;
use App\ValueObject\CategoryWorkflowState;
use App\ValueObjectInterface\CategoryWorkflowStateInterface;

final class CategoryWorkflow implements CategoryWorkflowInterface
{
    public function __construct(
        private readonly string $categoryId,
        private readonly CategoryWorkflowState $state,
        private readonly string $actorId,
        private readonly string $reason,
        private readonly \DateTimeImmutable $transitionedAt,
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

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function state(): CategoryWorkflowStateInterface
    {
        return $this->state;
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function transitionedAt(): \DateTimeImmutable
    {
        return $this->transitionedAt;
    }
}
