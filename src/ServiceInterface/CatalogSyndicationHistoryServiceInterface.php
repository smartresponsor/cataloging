<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\EventInterface\CategorySyndicationDestinationHistoryBuiltInterface;
use App\EventInterface\CategorySyndicationRecoveryAuditConsolidatedInterface;
/**
 * Defines the contract for catalog syndication history service.
 */
interface CatalogSyndicationHistoryServiceInterface
{
    /**
     * @param list<CategorySyndicationDeliveryRecordInterface> $records
     */
    public function buildDestinationHistory(
        string $destinationId,
        array $records,
        string $actorId,
        string $reason,
    ): CategorySyndicationDestinationHistoryBuiltInterface;

    /**
     * @param list<CategorySyndicationDeliveryRecordInterface> $records
     */
    public function consolidateRecoveryAudit(
        string $destinationId,
        array $records,
        string $actorId,
        string $reason,
    ): CategorySyndicationRecoveryAuditConsolidatedInterface;
}
