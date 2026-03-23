<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationDestinationGovernanceSummaryInterface;

final class CategorySyndicationDestinationGovernanceSummary implements CategorySyndicationDestinationGovernanceSummaryInterface
{
    /**
     * @param array<string,int>  $statusCounts
     * @param array<string,int>  $policyModeCounts
     * @param list<string>       $warningCodes
     * @param array<string,bool> $checks
     */
    public function __construct(
        private readonly string $destinationId,
        private readonly int $totalTrails,
        private readonly int $resolvedPublishableCount,
        private readonly int $fallbackUsedCount,
        private readonly int $retryableCount,
        private readonly int $retryScheduledCount,
        private readonly int $failureTrailCount,
        private readonly int $deliveredTrailCount,
        private readonly array $statusCounts,
        private readonly array $policyModeCounts,
        private readonly array $warningCodes,
        private readonly array $checks,
    ) {
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    public function totalTrails(): int
    {
        return $this->totalTrails;
    }

    public function resolvedPublishableCount(): int
    {
        return $this->resolvedPublishableCount;
    }

    public function fallbackUsedCount(): int
    {
        return $this->fallbackUsedCount;
    }

    public function retryableCount(): int
    {
        return $this->retryableCount;
    }

    public function retryScheduledCount(): int
    {
        return $this->retryScheduledCount;
    }

    public function failureTrailCount(): int
    {
        return $this->failureTrailCount;
    }

    public function deliveredTrailCount(): int
    {
        return $this->deliveredTrailCount;
    }

    public function statusCounts(): array
    {
        return $this->statusCounts;
    }

    public function policyModeCounts(): array
    {
        return $this->policyModeCounts;
    }

    public function warningCodes(): array
    {
        return $this->warningCodes;
    }

    public function checks(): array
    {
        return $this->checks;
    }
}
