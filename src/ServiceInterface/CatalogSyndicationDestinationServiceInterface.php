<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CatalogSyndicationDestinationRegisteredInterface;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationRegisterRequest;

/**
 * Defines the contract for catalog syndication destination service.
 */
interface CatalogSyndicationDestinationServiceInterface
{
    public function register(
        CatalogSyndicationDestinationRegisterRequest $request,
    ): CatalogSyndicationDestinationRegisteredInterface;
}
