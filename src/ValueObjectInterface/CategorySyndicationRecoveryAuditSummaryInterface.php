<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObjectInterface;

interface CategorySyndicationRecoveryAuditSummaryInterface
{
    public function destinationId(): string;

    public function totalFailed(): int;

    public function retryableFailed(): int;

    public function nonRetryableFailed(): int;

    public function scheduledRetries(): int;

    public function deliveredAfterRetry(): int;

    public function maxAttemptSeen(): int;

    /** @return list<string> */
    public function affectedCategoryIds(): array;
}
