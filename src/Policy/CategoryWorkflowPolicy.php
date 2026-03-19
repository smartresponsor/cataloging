<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Policy;

use App\PolicyInterface\CategoryWorkflowPolicyInterface;
use App\ValueObject\CategoryWorkflowState;
use App\ValueObjectInterface\CategoryWorkflowStateInterface;

final class CategoryWorkflowPolicy implements CategoryWorkflowPolicyInterface
{
    /** @var array<string,list<string>> */
    private const TRANSITIONS = [
        CategoryWorkflowState::DRAFT => [
            CategoryWorkflowState::DRAFT,
            CategoryWorkflowState::IN_REVIEW,
            CategoryWorkflowState::APPROVED,
            CategoryWorkflowState::ARCHIVED,
        ],
        CategoryWorkflowState::IN_REVIEW => [
            CategoryWorkflowState::DRAFT,
            CategoryWorkflowState::IN_REVIEW,
            CategoryWorkflowState::APPROVED,
            CategoryWorkflowState::ARCHIVED,
        ],
        CategoryWorkflowState::APPROVED => [
            CategoryWorkflowState::DRAFT,
            CategoryWorkflowState::APPROVED,
            CategoryWorkflowState::PUBLISHED,
            CategoryWorkflowState::ARCHIVED,
        ],
        CategoryWorkflowState::PUBLISHED => [
            CategoryWorkflowState::DRAFT,
            CategoryWorkflowState::PUBLISHED,
            CategoryWorkflowState::ARCHIVED,
        ],
        CategoryWorkflowState::ARCHIVED => [
            CategoryWorkflowState::DRAFT,
            CategoryWorkflowState::ARCHIVED,
        ],
    ];

    public function canTransition(CategoryWorkflowStateInterface $from, CategoryWorkflowStateInterface $to, string $actorId, string $reason): bool
    {
        $actorId = trim($actorId);
        $reason = trim($reason);

        if ('' === $actorId || '' === $reason) {
            return false;
        }

        $allowedTargets = self::TRANSITIONS[$from->value()] ?? [];

        return in_array($to->value(), $allowedTargets, true);
    }

    public function assertTransitionAllowed(CategoryWorkflowStateInterface $from, CategoryWorkflowStateInterface $to, string $actorId, string $reason): void
    {
        if (!$this->canTransition($from, $to, $actorId, $reason)) {
            throw new \DomainException(sprintf('Category workflow transition is not allowed: %s -> %s', $from->value(), $to->value()));
        }
    }
}
