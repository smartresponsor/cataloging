<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;
/**
 * Defines the contract for category syndication destination governance summary.
 */
interface CategorySyndicationDestinationGovernanceSummaryInterface
{
    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string;
    /**
     * Handles the total trails workflow.
     */
    public function totalTrails(): int;
    /**
     * Resolves the d publishable count result for the current workflow.
     */
    public function resolvedPublishableCount(): int;
    /**
     * Handles the fallback used count workflow.
     */
    public function fallbackUsedCount(): int;
    /**
     * Handles the retryable count workflow.
     */
    public function retryableCount(): int;
    /**
     * Handles the retry scheduled count workflow.
     */
    public function retryScheduledCount(): int;
    /**
     * Handles the failure trail count workflow.
     */
    public function failureTrailCount(): int;
    /**
     * Handles the delivered trail count workflow.
     */
    public function deliveredTrailCount(): int;

    /** @return array<string,int> */
    public function statusCounts(): array;

    /** @return array<string,int> */
    public function policyModeCounts(): array;

    /** @return list<string> */
    public function warningCodes(): array;

    /** @return array<string,bool> */
    public function checks(): array;
}
