<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategorySyndicationPublishPackageBuiltInterface;
use App\Cataloging\ValueObject\CategorySyndicationPackageBuildRequest;

/**
 * Defines the contract for catalog syndication mapping service.
 */
interface CatalogSyndicationMappingServiceInterface
{
    /**
     * Builds the publish package for a syndication request.
     */
    public function buildPublishPackage(
        CategorySyndicationPackageBuildRequest $request,
    ): CategorySyndicationPublishPackageBuiltInterface;
}
