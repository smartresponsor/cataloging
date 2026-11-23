<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\Cataloging\EventInterface\CategorySyndicationDestinationHistoryBuiltInterface;
use App\Cataloging\EventInterface\CategorySyndicationRecoveryAuditConsolidatedInterface;

interface CatalogSyndicationHistoryServiceInterface
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
