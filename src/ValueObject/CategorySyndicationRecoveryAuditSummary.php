<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationRecoveryAuditSummaryInterface;

final class CategorySyndicationRecoveryAuditSummary implements CategorySyndicationRecoveryAuditSummaryInterface
{
    /**
     * @param list<string> $affectedCategoryIds
     */
    public function __construct(
        private readonly string $destinationId,
        private readonly int $totalFailed,
        private readonly int $retryableFailed,
        private readonly int $nonRetryableFailed,
        private readonly int $scheduledRetries,
        private readonly int $deliveredAfterRetry,
        private readonly int $maxAttemptSeen,
        private readonly array $affectedCategoryIds,
    ) {
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    public function totalFailed(): int
    {
        return $this->totalFailed;
    }

    public function retryableFailed(): int
    {
        return $this->retryableFailed;
    }

    public function nonRetryableFailed(): int
    {
        return $this->nonRetryableFailed;
    }

    public function scheduledRetries(): int
    {
        return $this->scheduledRetries;
    }

    public function deliveredAfterRetry(): int
    {
        return $this->deliveredAfterRetry;
    }

    public function maxAttemptSeen(): int
    {
        return $this->maxAttemptSeen;
    }

    public function affectedCategoryIds(): array
    {
        return $this->affectedCategoryIds;
    }
}
