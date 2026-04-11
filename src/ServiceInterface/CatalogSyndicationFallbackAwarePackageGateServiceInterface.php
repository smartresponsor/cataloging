<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationFallbackAwarePackageGatedInterface;
use App\ValueObject\CategorySyndicationPackageBuildRequest;
/**
 * Defines the contract for catalog syndication fallback aware package gate service.
 */
interface CatalogSyndicationFallbackAwarePackageGateServiceInterface
{
    /**
     * Builds the fallback aware gated package result for a syndication request.
     */
    public function buildGatedPublishPackage(
        CategorySyndicationPackageBuildRequest $request,
    ): CategorySyndicationFallbackAwarePackageGatedInterface;
}
