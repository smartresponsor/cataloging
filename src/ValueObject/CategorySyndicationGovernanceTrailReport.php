<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationGovernanceTrailReportInterface;

/**
 * Represents the category syndication governance trail report value.
 */
final readonly class CategorySyndicationGovernanceTrailReport implements CategorySyndicationGovernanceTrailReportInterface
{
    /**
     * @param array<string,int>  $historyCounts
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     */
    public function __construct(
        private string $destinationId,
        private string $categoryId,
        private string $mediaPolicyMode,
        private bool $strictPublishable,
        private bool $fallbackPublishable,
        private bool $resolvedPublishable,
        private bool $fallbackUsed,
        private string $deliveryStatus,
        private bool $retryable,
        private bool $retryScheduled,
        private array $historyCounts,
        private array $warnings,
        private array $checks,
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
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the media policy mode workflow.
     */
    public function mediaPolicyMode(): string
    {
        return $this->mediaPolicyMode;
    }

    /**
     * Handles the strict publishable workflow.
     */
    public function strictPublishable(): bool
    {
        return $this->strictPublishable;
    }

    /**
     * Handles the fallback publishable workflow.
     */
    public function fallbackPublishable(): bool
    {
        return $this->fallbackPublishable;
    }

    /**
     * Resolves the d publishable result for the current workflow.
     */
    public function resolvedPublishable(): bool
    {
        return $this->resolvedPublishable;
    }

    /**
     * Handles the fallback used workflow.
     */
    public function fallbackUsed(): bool
    {
        return $this->fallbackUsed;
    }

    /**
     * Handles the delivery status workflow.
     */
    public function deliveryStatus(): string
    {
        return $this->deliveryStatus;
    }

    /**
     * Handles the retryable workflow.
     */
    public function retryable(): bool
    {
        return $this->retryable;
    }

    /**
     * Handles the retry scheduled workflow.
     */
    public function retryScheduled(): bool
    {
        return $this->retryScheduled;
    }

    /** @return array<string,int> */
    public function historyCounts(): array
    {
        return $this->historyCounts;
    }

    /** @return list<string> */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /** @return array<string,bool> */
    public function checks(): array
    {
        return $this->checks;
    }
}
