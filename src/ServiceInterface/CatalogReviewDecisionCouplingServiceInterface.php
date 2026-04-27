<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\Catalog\CatalogCategoryReviewDecisionCoupledEventInterface;
use App\Cataloging\ValueObject\CategoryReviewDecisionCouplingRequest;

/**
 * Defines the contract for catalog review decision coupling service.
 */
interface CatalogReviewDecisionCouplingServiceInterface
{
    public function couple(CategoryReviewDecisionCouplingRequest $request): CatalogCategoryReviewDecisionCoupledEventInterface;
}
