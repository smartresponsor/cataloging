<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Policy;

use App\PolicyInterface\CategoryChangeRequestPolicyInterface;
use App\ValueObject\CategoryChangeRequestState;
use App\ValueObjectInterface\CategoryChangeRequestStateInterface;

final class CategoryChangeRequestPolicy implements CategoryChangeRequestPolicyInterface
{
    /** @var array<string,list<string>> */
    private const REVIEW_TRANSITIONS = [
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

    public function canSubmit(string $requestId, string $categoryId, string $submittedBy, string $summary, array $changes): bool
    {
        if ('' === trim($requestId) || '' === trim($categoryId) || '' === trim($submittedBy) || '' === trim($summary)) {
            return false;
        }

        return [] !== $changes;
    }

    public function assertCanSubmit(string $requestId, string $categoryId, string $submittedBy, string $summary, array $changes): void
    {
        if (!$this->canSubmit($requestId, $categoryId, $submittedBy, $summary, $changes)) {
            throw new \DomainException('Category change request submission is not allowed.');
        }
    }

    public function canReview(CategoryChangeRequestStateInterface $from, CategoryChangeRequestStateInterface $to, string $reviewedBy, string $decisionReason): bool
    {
        if ('' === trim($reviewedBy) || '' === trim($decisionReason)) {
            return false;
        }

        $allowedTargets = self::REVIEW_TRANSITIONS[$from->value()] ?? [];

        return in_array($to->value(), $allowedTargets, true);
    }

    public function assertCanReview(CategoryChangeRequestStateInterface $from, CategoryChangeRequestStateInterface $to, string $reviewedBy, string $decisionReason): void
    {
        if (!$this->canReview($from, $to, $reviewedBy, $decisionReason)) {
            throw new \DomainException(sprintf('Category change request review transition is not allowed: %s -> %s', $from->value(), $to->value()));
        }
    }
}
