<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ServiceInterface;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\EventInterface\CategorySyndicationDestinationHistoryBuiltInterface;
use App\EventInterface\CategorySyndicationRecoveryAuditConsolidatedInterface;

interface CategorySyndicationHistoryServiceInterface
{
    /**
     * @param list<CategorySyndicationDeliveryRecordInterface> $records
     */
    public function buildDestinationHistory(string $destinationId, array $records, string $actorId, string $reason): CategorySyndicationDestinationHistoryBuiltInterface;

    /**
     * @param list<CategorySyndicationDeliveryRecordInterface> $records
     */
    public function consolidateRecoveryAudit(string $destinationId, array $records, string $actorId, string $reason): CategorySyndicationRecoveryAuditConsolidatedInterface;
}
