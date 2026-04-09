<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationRetryPolicyInterface;

/**
 * Provides the category syndication retry policy implementation.
 */
final class CategorySyndicationRetryPolicy implements CategorySyndicationRetryPolicyInterface
{
    /** @var array<int,int> */
    private const array DELAYS = [1 => 300, 2 => 900, 3 => 1800, 4 => 3600];

    /**
     * Determines whether the retryable condition is satisfied.
     */
    public function isRetryable(?int $responseCode): bool
    {
        return null === $responseCode || 429 === $responseCode || $responseCode >= 500;
    }

    /**
     * Handles the assert failed status workflow.
     */
    public function assertFailedStatus(string $status): void
    {
        if ('failed' !== trim($status)) {
            throw new \InvalidArgumentException('Retry scheduling is allowed only for failed deliveries.');
        }
    }

    /**
     * Handles the next attempt workflow.
     */
    public function nextAttempt(int $currentAttempt): int
    {
        if ($currentAttempt < 1) {
            throw new \InvalidArgumentException('Current attempt must be greater than zero.');
        }

        return $currentAttempt + 1;
    }

    /**
     * Handles the delay seconds for attempt workflow.
     */
    public function delaySecondsForAttempt(int $nextAttempt): int
    {
        if (!isset(self::DELAYS[$nextAttempt])) {
            throw new \InvalidArgumentException('Retry attempt exceeds supported retry schedule.');
        }

        return self::DELAYS[$nextAttempt];
    }
}
