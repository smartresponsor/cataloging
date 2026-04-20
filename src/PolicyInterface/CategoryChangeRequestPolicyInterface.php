<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObjectInterface\CategoryChangeRequestStateInterface;

/**
 * Defines the contract for category change request policy.
 */
interface CategoryChangeRequestPolicyInterface
{
    /** @param array<string,mixed> $changes */
    public function canSubmit(
        string $requestId,
        string $categoryId,
        string $submittedBy,
        string $summary,
        array $changes,
    ): bool;

    /** @param array<string,mixed> $changes */
    public function assertCanSubmit(
        string $requestId,
        string $categoryId,
        string $submittedBy,
        string $summary,
        array $changes,
    ): void;

    /**
     * Determines whether the current workflow can review.
     */
    public function canReview(
        CategoryChangeRequestStateInterface $from,
        CategoryChangeRequestStateInterface $to,
        string $reviewedBy,
        string $decisionReason,
    ): bool;

    /**
     * Handles the assert can review workflow.
     */
    public function assertCanReview(
        CategoryChangeRequestStateInterface $from,
        CategoryChangeRequestStateInterface $to,
        string $reviewedBy,
        string $decisionReason,
    ): void;
}
