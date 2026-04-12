<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

use App\ValueObjectInterface\CategorySyndicationCategoryGovernanceSummaryInterface;

/**
 * Defines the contract for category syndication category governance summary policy.
 */
/** @noinspection PhpClassNamingConventionInspection */
interface CategorySyndicationCategoryGovernanceSummaryPolicyInterface
{
    /** @param list<array<string,mixed>> $trailPayloads */
    public function buildSummary(
        string $categoryId,
        array $trailPayloads,
    ): CategorySyndicationCategoryGovernanceSummaryInterface;
}
