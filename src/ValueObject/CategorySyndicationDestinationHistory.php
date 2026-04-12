<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationDestinationHistoryInterface;

/**
 * Represents the category syndication destination history value.
 */
final readonly class CategorySyndicationDestinationHistory implements CategorySyndicationDestinationHistoryInterface
{
    /**
     * @param list<string> $packageIds
     * @param list<string> $categoryIds
     */
    public function __construct(
        private string $destinationId,
        private array $packageIds,
        private array $categoryIds,
        private int $totalRecords,
        private int $deliveredCount,
        private int $failedCount,
        private int $pendingCount,
        private int $retryScheduledCount,
        private int $skippedCount,
        private int $maxAttempt,
        private ?\DateTimeImmutable $latestDeliveredAt,
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
