<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategorySyndicationDestinationGovernanceSummaryBuiltInterface;
use App\Cataloging\ValueObject\CategorySyndicationDestinationGovernanceSummaryRequest;

/**
 * Defines the contract for catalog syndication destination governance summary service.
 */
/** @noinspection PhpClassNamingConventionInspection */
interface CatalogSyndicationDestinationGovernanceSummaryServiceInterface
{
    public function buildSummary(
        CategorySyndicationDestinationGovernanceSummaryRequest $request,
    ): CategorySyndicationDestinationGovernanceSummaryBuiltInterface;
}
