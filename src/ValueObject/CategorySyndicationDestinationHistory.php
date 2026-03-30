<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationDestinationHistoryInterface;

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

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    public function packageIds(): array
    {
        return $this->packageIds;
    }

    public function categoryIds(): array
    {
        return $this->categoryIds;
    }

    public function totalRecords(): int
    {
        return $this->totalRecords;
    }

    public function deliveredCount(): int
    {
        return $this->deliveredCount;
    }

    public function failedCount(): int
    {
        return $this->failedCount;
    }

    public function pendingCount(): int
    {
        return $this->pendingCount;
    }

    public function retryScheduledCount(): int
    {
        return $this->retryScheduledCount;
    }

    public function skippedCount(): int
    {
        return $this->skippedCount;
    }

    public function maxAttempt(): int
    {
        return $this->maxAttempt;
    }

    public function latestDeliveredAt(): ?\DateTimeImmutable
    {
        return $this->latestDeliveredAt;
    }
}
