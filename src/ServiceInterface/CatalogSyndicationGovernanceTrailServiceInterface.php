<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationGovernanceTrailRecordedInterface;
use App\ValueObject\CategorySyndicationGovernanceTrailRecordRequest;

/**
 * Defines the contract for catalog syndication governance trail service.
 */
interface CatalogSyndicationGovernanceTrailServiceInterface
{
    public function recordTrail(
        CategorySyndicationGovernanceTrailRecordRequest $request,
    ): CategorySyndicationGovernanceTrailRecordedInterface;
}
