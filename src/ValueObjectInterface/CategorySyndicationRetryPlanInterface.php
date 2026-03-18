<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObjectInterface;

interface CategorySyndicationRetryPlanInterface
{
    public function deliveryId(): string;

    public function packageId(): string;

    public function destinationId(): string;

    public function categoryId(): string;

    public function nextAttempt(): int;

    public function delaySeconds(): int;

    public function scheduledAt(): \DateTimeImmutable;

    public function retryable(): bool;
}
