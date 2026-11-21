<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CatalogCategoryWorkflowEntityPolicyInterface;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityState;
use App\Cataloging\ValueObjectInterface\CatalogCategoryWorkflowEntityStateInterface;

/**
 * Provides the category workflow policy implementation.
 */
final class CatalogCategoryWorkflowEntityPolicy implements CatalogCategoryWorkflowEntityPolicyInterface
{
    /** @var array<string,list<string>> */
    private const array TRANSITIONS = [
        CatalogCategoryWorkflowEntityState::DRAFT => [
            CatalogCategoryWorkflowEntityState::IN_REVIEW,
            CatalogCategoryWorkflowEntityState::APPROVED,
            CatalogCategoryWorkflowEntityState::ARCHIVED,
        ],
        CatalogCategoryWorkflowEntityState::IN_REVIEW => [
            CatalogCategoryWorkflowEntityState::DRAFT,
            CatalogCategoryWorkflowEntityState::APPROVED,
            CatalogCategoryWorkflowEntityState::ARCHIVED,
        ],
        CatalogCategoryWorkflowEntityState::APPROVED => [
            CatalogCategoryWorkflowEntityState::DRAFT,
            CatalogCategoryWorkflowEntityState::PUBLISHED,
            CatalogCategoryWorkflowEntityState::ARCHIVED,
        ],
        CatalogCategoryWorkflowEntityState::PUBLISHED => [
            CatalogCategoryWorkflowEntityState::DRAFT,
            CatalogCategoryWorkflowEntityState::ARCHIVED,
        ],
        CatalogCategoryWorkflowEntityState::ARCHIVED => [
            CatalogCategoryWorkflowEntityState::DRAFT,
        ],
    ];

    /**
     * Determines whether the current workflow can transition.
     */
    public function canTransition(
        CatalogCategoryWorkflowEntityStateInterface $from,
        CatalogCategoryWorkflowEntityStateInterface $to,
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
        CatalogCategoryWorkflowEntityStateInterface $from,
        CatalogCategoryWorkflowEntityStateInterface $to,
        string $actorId,
        string $reason,
    ): void {
        if (!$this->canTransition($from, $to, $actorId, $reason)) {
            throw new \DomainException(sprintf('CategoryEntity workflow transition is not allowed: %s -> %s', $from->value(), $to->value()));
        }
    }
}
