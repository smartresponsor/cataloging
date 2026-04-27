<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\EntityInterface\Catalog\CatalogSyndicationDeliveryRecordEntityInterface;

/**
 * Defines the contract for category syndication history policy.
 */
interface CategorySyndicationHistoryPolicyInterface
{
    /**
     * Handles the assert destination id workflow.
     */
    public function assertDestinationId(string $destinationId): void;

    /** @param list<CatalogSyndicationDeliveryRecordEntityInterface> $records
     * @return list<CatalogSyndicationDeliveryRecordEntityInterface>
     */
    public function recordsForDestination(string $destinationId, array $records): array;
}
