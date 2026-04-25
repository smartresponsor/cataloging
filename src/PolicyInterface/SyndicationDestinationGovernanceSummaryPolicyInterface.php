<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

use App\Cataloging\ValueObjectInterface\CatalogSyndicationDestinationGovernanceSummaryInterface;

/**
 * Defines the contract for category syndication destination governance summary policy.
 */
/** @noinspection PhpClassNamingConventionInspection */
interface CatalogSyndicationDestinationGovernanceSummaryPolicyInterface
{
    /** @param list<array<string,mixed>> $trailPayloads */
    public function buildSummary(
        string $destinationId,
        array $trailPayloads,
    ): CatalogSyndicationDestinationGovernanceSummaryInterface;
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationGovernanceSummaryPolicyInterface', false)) {
    class_alias(CatalogSyndicationDestinationGovernanceSummaryPolicyInterface::class, __NAMESPACE__.'\\SyndicationDestinationGovernanceSummaryPolicyInterface');
}
