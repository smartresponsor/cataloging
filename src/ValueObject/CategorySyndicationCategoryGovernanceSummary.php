<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationCategoryGovernanceSummaryInterface;

/**
 * Represents the category syndication category governance summary value.
 */
final readonly class CategorySyndicationCategoryGovernanceSummary implements CategorySyndicationCategoryGovernanceSummaryInterface
{
    /**
     * @param list<string>       $destinationIds
     * @param array<string,int>  $statusCounts
     * @param array<string,int>  $policyModeCounts
     * @param list<string>       $warningCodes
     * @param array<string,bool> $checks
     */
    public function __construct(
        private string $categoryId,
        private int $totalTrails,
        private int $resolvedPublishableCount,
        private int $fallbackUsedCount,
        private int $retryableCount,
        private int $retryScheduledCount,
        private int $failureTrailCount,
        private int $deliveredTrailCount,
        private array $destinationIds,
        private array $statusCounts,
        private array $policyModeCounts,
        private array $warningCodes,
        private array $checks,
    ) {
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
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

    /**
     * Handles the destination ids workflow.
     */
    public function destinationIds(): array
    {
        return $this->destinationIds;
    }

    /**
     * Handles the status counts workflow.
     */
    public function statusCounts(): array
    {
        return $this->statusCounts;
    }

    /**
     * Handles the policy mode counts workflow.
     */
    public function policyModeCounts(): array
    {
        return $this->policyModeCounts;
    }

    /**
     * Handles the warning codes workflow.
     */
    public function warningCodes(): array
    {
        return $this->warningCodes;
    }

    /**
     * Handles the checks workflow.
     */
    public function checks(): array
    {
        return $this->checks;
    }
}
