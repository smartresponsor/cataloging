<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\Catalog\CatalogCategorySyndicationGovernanceTrailRecordedEventInterface;
use App\Cataloging\ValueObject\CategorySyndicationGovernanceTrailRecordRequest;

/**
 * Defines the contract for catalog syndication governance trail service.
 */
interface CatalogSyndicationGovernanceTrailServiceInterface
{
    public function recordTrail(
        CategorySyndicationGovernanceTrailRecordRequest $request,
    ): CatalogCategorySyndicationGovernanceTrailRecordedEventInterface;
}
