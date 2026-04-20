<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObjectInterface;

/**
 * Defines the contract for category syndication governance trail report.
 */
interface CategorySyndicationGovernanceTrailReportInterface
{
    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string;

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the media policy mode workflow.
     */
    public function mediaPolicyMode(): string;

    /**
     * Handles the strict publishable workflow.
     */
    public function strictPublishable(): bool;

    /**
     * Handles the fallback publishable workflow.
     */
    public function fallbackPublishable(): bool;

    /**
     * Resolves the d publishable result for the current workflow.
     */
    public function resolvedPublishable(): bool;

    /**
     * Handles the fallback used workflow.
     */
    public function fallbackUsed(): bool;

    /**
     * Handles the delivery status workflow.
     */
    public function deliveryStatus(): string;

    /**
     * Handles the retryable workflow.
     */
    public function retryable(): bool;

    /**
     * Handles the retry scheduled workflow.
     */
    public function retryScheduled(): bool;

    /** @return array<string,int> */
    public function historyCounts(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;
}
