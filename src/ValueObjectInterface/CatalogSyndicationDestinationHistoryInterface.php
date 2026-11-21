<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObjectInterface;

/**
 * Defines the contract for category syndication destination history.
 */
interface CatalogSyndicationDestinationHistoryInterface
{
    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string;

    /** @return list<string> */
    public function packageIds(): array;

    /** @return list<string> */
    public function categoryIds(): array;

    /**
     * Handles the total records workflow.
     */
    public function totalRecords(): int;

    /**
     * Handles the delivered count workflow.
     */
    public function deliveredCount(): int;

    /**
     * Handles the failed count workflow.
     */
    public function failedCount(): int;

    /**
     * Handles the pending count workflow.
     */
    public function pendingCount(): int;

    /**
     * Handles the retry scheduled count workflow.
     */
    public function retryScheduledCount(): int;

    /**
     * Handles the skipped count workflow.
     */
    public function skippedCount(): int;

    /**
     * Handles the max attempt workflow.
     */
    public function maxAttempt(): int;

    /**
     * Handles the latest delivered at workflow.
     */
    public function latestDeliveredAt(): ?\DateTimeImmutable;
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationHistoryInterface', false)) {
    class_alias(CatalogSyndicationDestinationHistoryInterface::class, __NAMESPACE__.'\\SyndicationDestinationHistoryInterface');
}
