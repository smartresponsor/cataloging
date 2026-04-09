<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationRecoveryAuditSummaryInterface;
/**
 * Represents the category syndication recovery audit summary value.
 */
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
    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string
    {
        return $this->destinationId;
    }
    /**
     * Handles the total failed workflow.
     */
    public function totalFailed(): int
    {
        return $this->totalFailed;
    }
    /**
     * Handles the retryable failed workflow.
     */
    public function retryableFailed(): int
    {
        return $this->retryableFailed;
    }
    /**
     * Handles the non retryable failed workflow.
     */
    public function nonRetryableFailed(): int
    {
        return $this->nonRetryableFailed;
    }
    /**
     * Schedules the d retries workflow for later processing.
     */
    public function scheduledRetries(): int
    {
        return $this->scheduledRetries;
    }
    /**
     * Handles the delivered after retry workflow.
     */
    public function deliveredAfterRetry(): int
    {
        return $this->deliveredAfterRetry;
    }
    /**
     * Handles the max attempt seen workflow.
     */
    public function maxAttemptSeen(): int
    {
        return $this->maxAttemptSeen;
    }
    /**
     * Handles the affected category ids workflow.
     */
    public function affectedCategoryIds(): array
    {
        return $this->affectedCategoryIds;
    }
}
