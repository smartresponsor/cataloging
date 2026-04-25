<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EventInterface\CatalogSyndicationDestinationHistoryBuiltInterface;
use App\Cataloging\EventInterface\CategorySyndicationRecoveryAuditConsolidatedInterface;
use App\Cataloging\ValueObject\CategorySyndicationHistoryRequest;

/**
 * Defines the contract for catalog syndication history service.
 */
interface CatalogSyndicationHistoryServiceInterface
{
    public function buildDestinationHistory(
        CategorySyndicationHistoryRequest $request,
    ): CatalogSyndicationDestinationHistoryBuiltInterface;

    public function consolidateRecoveryAudit(
        CategorySyndicationHistoryRequest $request,
    ): CategorySyndicationRecoveryAuditConsolidatedInterface;
}
