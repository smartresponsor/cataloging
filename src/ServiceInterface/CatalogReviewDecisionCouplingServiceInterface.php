<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryReviewDecisionCoupledInterface;
/**
 * Defines the contract for catalog review decision coupling service.
 */
interface CatalogReviewDecisionCouplingServiceInterface
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
