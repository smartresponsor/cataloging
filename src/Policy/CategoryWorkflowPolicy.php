<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryWorkflowPolicyInterface;
use App\ValueObject\CatalogCategoryWorkflowState;
use App\ValueObjectInterface\CatalogCategoryWorkflowStateInterface;

/**
 * Provides the category workflow policy implementation.
 */
final class CategoryWorkflowPolicy implements CategoryWorkflowPolicyInterface
{
    /** @var array<string,list<string>> */
    private const array TRANSITIONS = [
        CatalogCategoryWorkflowState::DRAFT => [
            CatalogCategoryWorkflowState::IN_REVIEW,
            CatalogCategoryWorkflowState::APPROVED,
            CatalogCategoryWorkflowState::ARCHIVED,
        ],
        CatalogCategoryWorkflowState::IN_REVIEW => [
            CatalogCategoryWorkflowState::DRAFT,
            CatalogCategoryWorkflowState::APPROVED,
            CatalogCategoryWorkflowState::ARCHIVED,
        ],
        CatalogCategoryWorkflowState::APPROVED => [
            CatalogCategoryWorkflowState::DRAFT,
            CatalogCategoryWorkflowState::PUBLISHED,
            CatalogCategoryWorkflowState::ARCHIVED,
        ],
        CatalogCategoryWorkflowState::PUBLISHED => [
            CatalogCategoryWorkflowState::DRAFT,
            CatalogCategoryWorkflowState::ARCHIVED,
        ],
        CatalogCategoryWorkflowState::ARCHIVED => [
            CatalogCategoryWorkflowState::DRAFT,
        ],
    ];

    /**
     * Determines whether the current workflow can transition.
     */
    public function canTransition(
        CatalogCategoryWorkflowStateInterface $from,
        CatalogCategoryWorkflowStateInterface $to,
        string $actorId,
        string $reason,
    ): bool {
        $actorId = trim($actorId);
        $reason = trim($reason);

        if ('' === $actorId || '' === $reason) {
            return false;
        }

        if ($from->value() === $to->value()) {
            return true;
        }

        $allowedTargets = self::TRANSITIONS[$from->value()] ?? [];

        return in_array($to->value(), $allowedTargets, true);
    }

    /**
     * Handles the assert transition allowed workflow.
     */
    public function assertTransitionAllowed(
        CatalogCategoryWorkflowStateInterface $from,
        CatalogCategoryWorkflowStateInterface $to,
        string $actorId,
        string $reason,
    ): void {
        if (!$this->canTransition($from, $to, $actorId, $reason)) {
            throw new \DomainException(sprintf('Category workflow transition is not allowed: %s -> %s', $from->value(), $to->value()));
        }
    }
}
