<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\Catalog\CatalogSyndicationDeliveryRecordedEventInterface;
use App\Cataloging\ValueObject\CatalogSyndicationDeliveryRecordRequest;

/**
 * Defines the contract for catalog syndication delivery service.
 */
interface CatalogSyndicationDeliveryServiceInterface
{
    /**
     * Handles the record delivery workflow.
     */
    public function recordDelivery(
        CatalogSyndicationDeliveryRecordRequest $request,
    ): CatalogSyndicationDeliveryRecordedEventInterface;
}
