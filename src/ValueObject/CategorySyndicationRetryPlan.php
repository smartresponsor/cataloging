<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationRetryPlanInterface;

/**
 * Represents the category syndication retry plan value.
 */
final readonly class CategorySyndicationRetryPlan implements CategorySyndicationRetryPlanInterface
{
    /**
     * Initializes the category syndication retry plan service collaborators.
     */
    public function __construct(
        private string $deliveryId,
        private string $packageId,
        private string $destinationId,
        private string $categoryId,
        private int $nextAttempt,
        private int $delaySeconds,
        private \DateTimeImmutable $scheduledAt,
        private bool $retryable,
    ) {
    }

    /**
     * Handles the delivery id workflow.
     */
    public function deliveryId(): string
    {
        return $this->deliveryId;
    }

    /**
     * Handles the package id workflow.
     */
    public function packageId(): string
    {
        return $this->packageId;
    }

    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string
    {
        return $this->destinationId;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the next attempt workflow.
     */
    public function nextAttempt(): int
    {
        return $this->nextAttempt;
    }

    /**
     * Handles the delay seconds workflow.
     */
    public function delaySeconds(): int
    {
        return $this->delaySeconds;
    }

    /**
     * Schedules the d at workflow for later processing.
     */
    public function scheduledAt(): \DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    /**
     * Handles the retryable workflow.
     */
    public function retryable(): bool
    {
        return $this->retryable;
    }
}
