<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationPolicyAwarePackageGatedInterface;
use App\ValueObject\CategorySyndicationPackageBuildRequest;
/**
 * Defines the contract for catalog syndication policy aware package gate service.
 */
interface CatalogSyndicationPolicyAwarePackageGateServiceInterface
{
    /**
     * Builds the policy aware gated package result for a syndication request.
     */
    public function buildGatedPublishPackage(
        CategorySyndicationPackageBuildRequest $request,
    ): CategorySyndicationPolicyAwarePackageGatedInterface;
}
