<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategorySyndicationDeliveryRecordedInterface;
use App\Cataloging\ValueObject\CategorySyndicationDeliveryRecordRequest;

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
