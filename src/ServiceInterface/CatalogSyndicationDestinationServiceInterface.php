<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationDestinationRegisteredInterface;
use App\ValueObject\CategorySyndicationDestinationRegisterRequest;

/**
 * Defines the contract for catalog syndication destination service.
 */
interface CatalogSyndicationDestinationServiceInterface
{
    public function register(
        CategorySyndicationDestinationRegisterRequest $request,
    ): CategorySyndicationDestinationRegisteredInterface;
}
