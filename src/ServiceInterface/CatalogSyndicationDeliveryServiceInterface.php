<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationDeliveryRecordedInterface;
/**
 * Defines the contract for catalog syndication delivery service.
 */
interface CatalogSyndicationDeliveryServiceInterface
{
    /**
     * Handles the record delivery workflow.
     */
    public function recordDelivery(
        string $deliveryId,
        string $packageId,
        string $destinationId,
        string $categoryId,
        string $status,
        int $attempt,
        ?int $responseCode,
        string $responseMessage,
        string $actorId,
        string $reason,
    ): CategorySyndicationDeliveryRecordedInterface;
}
