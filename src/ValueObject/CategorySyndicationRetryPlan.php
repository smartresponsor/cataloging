<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationRetryPlanInterface;

final class CategorySyndicationRetryPlan implements CategorySyndicationRetryPlanInterface
{
    public function __construct(
        private readonly string $deliveryId,
        private readonly string $packageId,
        private readonly string $destinationId,
        private readonly string $categoryId,
        private readonly int $nextAttempt,
        private readonly int $delaySeconds,
        private readonly \DateTimeImmutable $scheduledAt,
        private readonly bool $retryable,
    ) {
    }

    public function deliveryId(): string
    {
        return $this->deliveryId;
    }

    public function packageId(): string
    {
        return $this->packageId;
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function nextAttempt(): int
    {
        return $this->nextAttempt;
    }

    public function delaySeconds(): int
    {
        return $this->delaySeconds;
    }

    public function scheduledAt(): \DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function retryable(): bool
    {
        return $this->retryable;
    }
}
