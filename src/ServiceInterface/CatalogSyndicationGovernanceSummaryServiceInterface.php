<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategorySyndicationCategoryGovernanceSummaryBuiltInterface;
use App\Cataloging\ValueObject\CategorySyndicationGovernanceSummaryRequest;

/**
 * Defines the contract for catalog syndication governance summary service.
 */
interface CatalogSyndicationGovernanceSummaryServiceInterface
{
    public function buildSummary(
        CategorySyndicationGovernanceSummaryRequest $request,
    ): CategorySyndicationCategoryGovernanceSummaryBuiltInterface;
}
