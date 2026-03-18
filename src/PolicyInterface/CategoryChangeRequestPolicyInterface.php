<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategoryChangeRequestStateInterface;

interface CategoryChangeRequestPolicyInterface
{
    /** @param array<string,mixed> $changes */
    public function canSubmit(string $requestId, string $categoryId, string $submittedBy, string $summary, array $changes): bool;

    /** @param array<string,mixed> $changes */
    public function assertCanSubmit(string $requestId, string $categoryId, string $submittedBy, string $summary, array $changes): void;

    public function canReview(CategoryChangeRequestStateInterface $from, CategoryChangeRequestStateInterface $to, string $reviewedBy, string $decisionReason): bool;

    public function assertCanReview(CategoryChangeRequestStateInterface $from, CategoryChangeRequestStateInterface $to, string $reviewedBy, string $decisionReason): void;
}
