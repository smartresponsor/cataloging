<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EntityInterface\Catalog\CatalogSyndicationDeliveryRecordEntityInterface;
use App\Cataloging\EventInterface\Catalog\CatalogCategorySyndicationRecoveryCandidatePreparedEventInterface;
use App\Cataloging\EventInterface\Catalog\CatalogCategorySyndicationRetryScheduledEventInterface;

/**
 * Defines the contract for catalog syndication retry service.
 */
interface CatalogSyndicationRetryServiceInterface
{
    /**
     * Handles the prepare recovery candidate workflow.
     */
    public function prepareRecoveryCandidate(
        CatalogSyndicationDeliveryRecordEntityInterface $record,
        string $actorId,
        string $reason,
    ): CatalogCategorySyndicationRecoveryCandidatePreparedEventInterface;

    /**
     * Schedules the retry workflow for later processing.
     */
    public function scheduleRetry(
        CatalogSyndicationDeliveryRecordEntityInterface $record,
        string $actorId,
        string $reason,
    ): CatalogCategorySyndicationRetryScheduledEventInterface;
}
