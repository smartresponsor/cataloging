<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationDestinationHistoryInterface;

/**
 * Represents the category syndication destination history value.
 */
final class CategorySyndicationDestinationHistory implements CategorySyndicationDestinationHistoryInterface
{
    /**
     * @param list<string> $packageIds
     * @param list<string> $categoryIds
     */
    public function __construct(
        private readonly string $destinationId,
        private readonly array $packageIds,
        private readonly array $categoryIds,
        private readonly int $totalRecords,
        private readonly int $deliveredCount,
        private readonly int $failedCount,
        private readonly int $pendingCount,
        private readonly int $retryScheduledCount,
        private readonly int $skippedCount,
        private readonly int $maxAttempt,
        private readonly ?\DateTimeImmutable $latestDeliveredAt,
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
     * Handles the package ids workflow.
     */
    public function packageIds(): array
    {
        return $this->packageIds;
    }

    /**
     * Handles the category ids workflow.
     */
    public function categoryIds(): array
    {
        return $this->categoryIds;
    }

    /**
     * Handles the total records workflow.
     */
    public function totalRecords(): int
    {
        return $this->totalRecords;
    }

    /**
     * Handles the delivered count workflow.
     */
    public function deliveredCount(): int
    {
        return $this->deliveredCount;
    }

    /**
     * Handles the failed count workflow.
     */
    public function failedCount(): int
    {
        return $this->failedCount;
    }

    /**
     * Handles the pending count workflow.
     */
    public function pendingCount(): int
    {
        return $this->pendingCount;
    }

    /**
     * Handles the retry scheduled count workflow.
     */
    public function retryScheduledCount(): int
    {
        return $this->retryScheduledCount;
    }

    /**
     * Handles the skipped count workflow.
     */
    public function skippedCount(): int
    {
        return $this->skippedCount;
    }

    /**
     * Handles the max attempt workflow.
     */
    public function maxAttempt(): int
    {
        return $this->maxAttempt;
    }

    /**
     * Handles the latest delivered at workflow.
     */
    public function latestDeliveredAt(): ?\DateTimeImmutable
    {
        return $this->latestDeliveredAt;
    }
}
