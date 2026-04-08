<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationGovernanceTrailReportInterface;
/**
 * Defines the contract for category syndication governance trail policy.
 */
interface CategorySyndicationGovernanceTrailPolicyInterface
{
    /**
     * @param array<string,mixed> $policyAwarePayload
     * @param array<string,mixed> $deliveryPayload
     * @param array<string,mixed> $historyPayload
     * @param array<string,mixed> $recoveryPayload
     */
    public function buildReport(array $policyAwarePayload, array $deliveryPayload, array $historyPayload, array $recoveryPayload): CategorySyndicationGovernanceTrailReportInterface;
}
