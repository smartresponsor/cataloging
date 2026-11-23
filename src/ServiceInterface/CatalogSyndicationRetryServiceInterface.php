<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\Cataloging\EventInterface\CategorySyndicationRecoveryCandidatePreparedInterface;
use App\Cataloging\EventInterface\CategorySyndicationRetryScheduledInterface;

interface CatalogSyndicationRetryServiceInterface
{
    public function prepareRecoveryCandidate(CategorySyndicationDeliveryRecordInterface $record, string $actorId, string $reason): CategorySyndicationRecoveryCandidatePreparedInterface;

    public function scheduleRetry(CategorySyndicationDeliveryRecordInterface $record, string $actorId, string $reason): CategorySyndicationRetryScheduledInterface;
}
