<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationDeliveryRecordedInterface;

interface CategorySyndicationDeliveryServiceInterface
{
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
