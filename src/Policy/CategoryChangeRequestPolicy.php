<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryChangeRequestPolicyInterface;
use App\ValueObject\CategoryChangeRequestState;
use App\ValueObjectInterface\CategoryChangeRequestStateInterface;

/**
 * Provides the category change request policy implementation.
 */
final class CategoryChangeRequestPolicy implements CategoryChangeRequestPolicyInterface
{
    /** @var array<string,list<string>> */
    private const array REVIEW_TRANSITIONS = [
        CategoryChangeRequestState::PROPOSED => [
            CategoryChangeRequestState::IN_REVIEW,
            CategoryChangeRequestState::ACCEPTED,
            CategoryChangeRequestState::REJECTED,
            CategoryChangeRequestState::WITHDRAWN,
        ],
        CategoryChangeRequestState::IN_REVIEW => [
            CategoryChangeRequestState::ACCEPTED,
            CategoryChangeRequestState::REJECTED,
            CategoryChangeRequestState::WITHDRAWN,
        ],
    ];

    /**
     * Determines whether the current workflow can submit.
     */
    public function canSubmit(
        string $requestId,
        string $categoryId,
        string $submittedBy,
        string $summary,
        array $changes,
    ): bool {
        if ('' === trim($requestId) || '' === trim($categoryId) || '' === trim($submittedBy) || '' === trim($summary)) {
            return false;
        }

        return [] !== $changes;
    }

    /**
     * Handles the assert can submit workflow.
     */
    public function assertCanSubmit(
        string $requestId,
        string $categoryId,
        string $submittedBy,
        string $summary,
        array $changes,
    ): void {
        if (!$this->canSubmit($requestId, $categoryId, $submittedBy, $summary, $changes)) {
            throw new \DomainException('Category change request submission is not allowed.');
        }
    }

    /**
     * Determines whether the current workflow can review.
     */
    public function canReview(
        CategoryChangeRequestStateInterface $from,
        CategoryChangeRequestStateInterface $to,
        string $reviewedBy,
        string $decisionReason,
    ): bool {
        if ('' === trim($reviewedBy) || '' === trim($decisionReason)) {
            return false;
        }

        $allowedTargets = self::REVIEW_TRANSITIONS[$from->value()] ?? [];

        return in_array($to->value(), $allowedTargets, true);
    }

    /**
     * Handles the assert can review workflow.
     */
    public function assertCanReview(
        CategoryChangeRequestStateInterface $from,
        CategoryChangeRequestStateInterface $to,
        string $reviewedBy,
        string $decisionReason,
    ): void {
        if (!$this->canReview($from, $to, $reviewedBy, $decisionReason)) {
            throw new \DomainException(sprintf('Category change request review transition is not allowed: %s -> %s', $from->value(), $to->value()));
        }
    }
}
