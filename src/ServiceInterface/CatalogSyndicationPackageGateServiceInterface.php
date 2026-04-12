<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationPackageGatedInterface;
use App\ValueObject\CategorySyndicationPackageBuildRequest;

/**
 * Defines the contract for catalog syndication package gate service.
 */
interface CatalogSyndicationPackageGateServiceInterface
{
    /**
     * Builds the gated publish package result for a syndication request.
     */
    public function buildGatedPublishPackage(
        CategorySyndicationPackageBuildRequest $request,
    ): CategorySyndicationPackageGatedInterface;
}
