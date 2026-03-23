<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

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
