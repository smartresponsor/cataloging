<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\Catalog\CatalogCategorySyndicationFallbackAwarePackageGatedEventInterface;
use App\Cataloging\ValueObject\CategorySyndicationPackageBuildRequest;

/**
 * Defines the contract for catalog syndication fallback aware package gate service.
 */
/** @noinspection PhpClassNamingConventionInspection */
interface CatalogSyndicationFallbackAwarePackageGateServiceInterface
{
    /**
     * Builds the fallback aware gated package result for a syndication request.
     */
    public function buildGatedPublishPackage(
        CategorySyndicationPackageBuildRequest $request,
    ): CatalogCategorySyndicationFallbackAwarePackageGatedEventInterface;
}
