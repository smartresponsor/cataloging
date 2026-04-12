<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationDeliveryRecordedInterface;
use App\ValueObject\CategorySyndicationDeliveryRecordRequest;

/**
 * Defines the contract for catalog syndication delivery service.
 */
interface CatalogSyndicationDeliveryServiceInterface
{
    /**
     * Handles the record delivery workflow.
     */
    public function recordDelivery(
        CategorySyndicationDeliveryRecordRequest $request,
    ): CategorySyndicationDeliveryRecordedInterface;
}
