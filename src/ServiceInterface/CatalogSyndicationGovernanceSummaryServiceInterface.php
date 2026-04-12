<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationCategoryGovernanceSummaryBuiltInterface;
use App\ValueObject\CategorySyndicationGovernanceSummaryRequest;

/**
 * Defines the contract for catalog syndication governance summary service.
 */
interface CatalogSyndicationGovernanceSummaryServiceInterface
{
    public function buildSummary(
        CategorySyndicationGovernanceSummaryRequest $request,
    ): CategorySyndicationCategoryGovernanceSummaryBuiltInterface;
}
