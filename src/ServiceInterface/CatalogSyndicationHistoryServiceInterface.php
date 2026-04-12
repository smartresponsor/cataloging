<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationDestinationHistoryBuiltInterface;
use App\EventInterface\CategorySyndicationRecoveryAuditConsolidatedInterface;
use App\ValueObject\CategorySyndicationHistoryRequest;

/**
 * Defines the contract for catalog syndication history service.
 */
interface CatalogSyndicationHistoryServiceInterface
{
    public function buildDestinationHistory(
        CategorySyndicationHistoryRequest $request,
    ): CategorySyndicationDestinationHistoryBuiltInterface;

    public function consolidateRecoveryAudit(
        CategorySyndicationHistoryRequest $request,
    ): CategorySyndicationRecoveryAuditConsolidatedInterface;
}
