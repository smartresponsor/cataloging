<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

/**
 * Defines the contract for category syndication recovery audit summary.
 */
interface CategorySyndicationRecoveryAuditSummaryInterface
{
    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string;

    /**
     * Handles the total failed workflow.
     */
    public function totalFailed(): int;

    /**
     * Handles the retryable failed workflow.
     */
    public function retryableFailed(): int;

    /**
     * Handles the non retryable failed workflow.
     */
    public function nonRetryableFailed(): int;

    /**
     * Schedules the d retries workflow for later processing.
     */
    public function scheduledRetries(): int;

    /**
     * Handles the delivered after retry workflow.
     */
    public function deliveredAfterRetry(): int;

    /**
     * Handles the max attempt seen workflow.
     */
    public function maxAttemptSeen(): int;

    /** @return list<string> */
    public function affectedCategoryIds(): array;
}
