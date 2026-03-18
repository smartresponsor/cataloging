<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObjectInterface;

interface CategorySyndicationDestinationHistoryInterface
{
    public function destinationId(): string;

    /** @return list<string> */
    public function packageIds(): array;

    /** @return list<string> */
    public function categoryIds(): array;

    public function totalRecords(): int;

    public function deliveredCount(): int;

    public function failedCount(): int;

    public function pendingCount(): int;

    public function retryScheduledCount(): int;

    public function skippedCount(): int;

    public function maxAttempt(): int;

    public function latestDeliveredAt(): ?\DateTimeImmutable;
}
