<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ServiceInterface;

use App\EventInterface\CategoryReviewDecisionCoupledInterface;

interface CategoryReviewDecisionCouplingServiceInterface
{
    /** @param array<string,bool> $checks */
    public function couple(
        string $requestId,
        string $targetState,
        string $reviewedBy,
        string $decisionReason,
        array $checks = [],
    ): CategoryReviewDecisionCoupledInterface;
}
