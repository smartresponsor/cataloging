<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategorySyndicationDestinationRegisteredInterface;
use App\Cataloging\ValueObject\CategorySyndicationDestinationRegisterRequest;

/**
 * Defines the contract for catalog syndication destination service.
 */
interface CatalogSyndicationDestinationServiceInterface
{
    public function register(
        CategorySyndicationDestinationRegisterRequest $request,
    ): CategorySyndicationDestinationRegisteredInterface;
}
