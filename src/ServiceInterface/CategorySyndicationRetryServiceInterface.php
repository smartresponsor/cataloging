<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ServiceInterface;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\EventInterface\CategorySyndicationRecoveryCandidatePreparedInterface;
use App\EventInterface\CategorySyndicationRetryScheduledInterface;

interface CategorySyndicationRetryServiceInterface
{
    public function prepareRecoveryCandidate(CategorySyndicationDeliveryRecordInterface $record, string $actorId, string $reason): CategorySyndicationRecoveryCandidatePreparedInterface;

    public function scheduleRetry(CategorySyndicationDeliveryRecordInterface $record, string $actorId, string $reason): CategorySyndicationRetryScheduledInterface;
}
