<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategorySyndicationRetryPolicyInterface;

final class CategorySyndicationRetryPolicy implements CategorySyndicationRetryPolicyInterface
{
    /** @var array<int,int> */
    private const DELAYS = [1 => 300, 2 => 900, 3 => 1800, 4 => 3600];

    public function isRetryable(?int $responseCode): bool
    {
        return null === $responseCode || 429 === $responseCode || $responseCode >= 500;
    }

    public function assertFailedStatus(string $status): void
    {
        if ('failed' !== trim($status)) {
            throw new \InvalidArgumentException('Retry scheduling is allowed only for failed deliveries.');
        }
    }

    public function nextAttempt(int $currentAttempt): int
    {
        if ($currentAttempt < 1) {
            throw new \InvalidArgumentException('Current attempt must be greater than zero.');
        }

        return $currentAttempt + 1;
    }

    public function delaySecondsForAttempt(int $nextAttempt): int
    {
        if (!isset(self::DELAYS[$nextAttempt])) {
            throw new \InvalidArgumentException('Retry attempt exceeds supported retry schedule.');
        }

        return self::DELAYS[$nextAttempt];
    }
}
