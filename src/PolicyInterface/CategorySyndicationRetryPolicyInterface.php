<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\PolicyInterface;

/**
 * Defines the contract for category syndication retry policy.
 */
interface CategorySyndicationRetryPolicyInterface
{
    /**
     * Determines whether the retryable condition is satisfied.
     */
    public function isRetryable(?int $responseCode): bool;

    /**
     * Handles the assert failed status workflow.
     */
    public function assertFailedStatus(string $status): void;

    /**
     * Handles the next attempt workflow.
     */
    public function nextAttempt(int $currentAttempt): int;

    /**
     * Handles the delay seconds for attempt workflow.
     */
    public function delaySecondsForAttempt(int $nextAttempt): int;
}
