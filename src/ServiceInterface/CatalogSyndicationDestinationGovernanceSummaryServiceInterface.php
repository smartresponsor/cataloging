<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CatalogSyndicationDestinationGovernanceSummaryBuiltInterface;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationGovernanceSummaryRequest;

/**
 * Defines the contract for catalog syndication destination governance summary service.
 */
/** @noinspection PhpClassNamingConventionInspection */
interface CatalogSyndicationDestinationGovernanceSummaryServiceInterface
{
    public function buildSummary(
        CatalogSyndicationDestinationGovernanceSummaryRequest $request,
    ): CatalogSyndicationDestinationGovernanceSummaryBuiltInterface;
}
