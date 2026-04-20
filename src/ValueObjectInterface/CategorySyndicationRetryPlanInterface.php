<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObjectInterface;

/**
 * Defines the contract for category syndication retry plan.
 */
interface CategorySyndicationRetryPlanInterface
{
    /**
     * Handles the delivery id workflow.
     */
    public function deliveryId(): string;

    /**
     * Handles the package id workflow.
     */
    public function packageId(): string;

    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string;

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the next attempt workflow.
     */
    public function nextAttempt(): int;

    /**
     * Handles the delay seconds workflow.
     */
    public function delaySeconds(): int;

    /**
     * Schedules the d at workflow for later processing.
     */
    public function scheduledAt(): \DateTimeImmutable;

    /**
     * Handles the retryable workflow.
     */
    public function retryable(): bool;
}
