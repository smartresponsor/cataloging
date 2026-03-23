<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;

interface CategorySyndicationHistoryPolicyInterface
{
    public function assertDestinationId(string $destinationId): void;

    /** @param list<CategorySyndicationDeliveryRecordInterface> $records
     * @return list<CategorySyndicationDeliveryRecordInterface>
     */
    public function recordsForDestination(string $destinationId, array $records): array;
}
