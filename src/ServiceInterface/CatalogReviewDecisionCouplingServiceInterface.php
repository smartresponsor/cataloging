<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryReviewDecisionCoupledInterface;
use App\ValueObject\CategoryReviewDecisionCouplingRequest;

/**
 * Defines the contract for catalog review decision coupling service.
 */
interface CatalogReviewDecisionCouplingServiceInterface
{
    public function couple(CategoryReviewDecisionCouplingRequest $request): CategoryReviewDecisionCoupledInterface;
}
