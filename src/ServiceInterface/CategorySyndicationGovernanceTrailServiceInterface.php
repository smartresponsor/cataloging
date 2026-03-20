<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ServiceInterface;

use App\EventInterface\CategorySyndicationGovernanceTrailRecordedInterface;

interface CategorySyndicationGovernanceTrailServiceInterface
{
    public function recordTrail(array $policyAwarePayload, array $deliveryPayload, array $historyPayload, array $recoveryPayload, string $actorId, string $reason): CategorySyndicationGovernanceTrailRecordedInterface;
}
