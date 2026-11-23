<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CategorySyndicationDeliveryRecordedInterface;

interface CatalogSyndicationDeliveryServiceInterface
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
