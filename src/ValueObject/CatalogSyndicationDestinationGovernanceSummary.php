<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CatalogSyndicationDestinationGovernanceSummaryInterface;

/**
 * Represents the category syndication destination governance summary value.
 */
final readonly class CatalogSyndicationDestinationGovernanceSummary implements CatalogSyndicationDestinationGovernanceSummaryInterface
{
    /**
     * @param array<string,int>  $statusCounts
     * @param array<string,int>  $policyModeCounts
     * @param list<string>       $warningCodes
     * @param array<string,bool> $checks
     */
    public function __construct(
        private string $destinationId,
        private int $totalTrails,
        private int $resolvedPublishableCount,
        private int $fallbackUsedCount,
        private int $retryableCount,
        private int $retryScheduledCount,
        private int $failureTrailCount,
        private int $deliveredTrailCount,
        private array $statusCounts,
        private array $policyModeCounts,
        private array $warningCodes,
        private array $checks,
    ) {
    }

    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string
    {
        return $this->destinationId;
    }

    /**
     * Handles the total trails workflow.
     */
    public function totalTrails(): int
    {
        return $this->totalTrails;
    }

    /**
     * Resolves the d publishable count result for the current workflow.
     */
    public function resolvedPublishableCount(): int
    {
        return $this->resolvedPublishableCount;
    }

    /**
     * Handles the fallback used count workflow.
     */
    public function fallbackUsedCount(): int
    {
        return $this->fallbackUsedCount;
    }

    /**
     * Handles the retryable count workflow.
     */
    public function retryableCount(): int
    {
        return $this->retryableCount;
    }

    /**
     * Handles the retry scheduled count workflow.
     */
    public function retryScheduledCount(): int
    {
        return $this->retryScheduledCount;
    }

    /**
     * Handles the failure trail count workflow.
     */
    public function failureTrailCount(): int
    {
        return $this->failureTrailCount;
    }

    /**
     * Handles the delivered trail count workflow.
     */
    public function deliveredTrailCount(): int
    {
        return $this->deliveredTrailCount;
    }

    /** @return array<string,int> */
    public function statusCounts(): array
    {
        return $this->statusCounts;
    }

    /** @return array<string,int> */
    public function policyModeCounts(): array
    {
        return $this->policyModeCounts;
    }

    /** @return list<string> */
    public function warningCodes(): array
    {
        return $this->warningCodes;
    }

    /** @return array<string,bool> */
    public function checks(): array
    {
        return $this->checks;
    }
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationGovernanceSummary', false)) {
    class_alias(CatalogSyndicationDestinationGovernanceSummary::class, __NAMESPACE__.'\\SyndicationDestinationGovernanceSummary');
}
