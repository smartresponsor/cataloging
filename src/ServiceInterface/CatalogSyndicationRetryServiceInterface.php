<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EntityInterface\CatalogSyndicationDeliveryRecordInterface;
use App\Cataloging\EventInterface\CategorySyndicationRecoveryCandidatePreparedInterface;
use App\Cataloging\EventInterface\CategorySyndicationRetryScheduledInterface;

/**
 * Defines the contract for catalog syndication retry service.
 */
interface CatalogSyndicationRetryServiceInterface
{
    /**
     * Handles the prepare recovery candidate workflow.
     */
    public function prepareRecoveryCandidate(
        CatalogSyndicationDeliveryRecordInterface $record,
        string $actorId,
        string $reason,
    ): CategorySyndicationRecoveryCandidatePreparedInterface;

    /**
     * Schedules the retry workflow for later processing.
     */
    public function scheduleRetry(
        CatalogSyndicationDeliveryRecordInterface $record,
        string $actorId,
        string $reason,
    ): CategorySyndicationRetryScheduledInterface;
}
