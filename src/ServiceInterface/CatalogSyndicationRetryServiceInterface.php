<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\EventInterface\CategorySyndicationRecoveryCandidatePreparedInterface;
use App\EventInterface\CategorySyndicationRetryScheduledInterface;
/**
 * Defines the contract for catalog syndication retry service.
 */
interface CatalogSyndicationRetryServiceInterface
{
    /**
     * Handles the prepare recovery candidate workflow.
     */
    public function prepareRecoveryCandidate(CategorySyndicationDeliveryRecordInterface $record, string $actorId, string $reason): CategorySyndicationRecoveryCandidatePreparedInterface;
    /**
     * Schedules the retry workflow for later processing.
     */
    public function scheduleRetry(CategorySyndicationDeliveryRecordInterface $record, string $actorId, string $reason): CategorySyndicationRetryScheduledInterface;
}
