<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\PolicyInterface;

interface CategorySyndicationRetryPolicyInterface
{
    public function isRetryable(?int $responseCode): bool;

    public function assertFailedStatus(string $status): void;

    public function nextAttempt(int $currentAttempt): int;

    public function delaySecondsForAttempt(int $nextAttempt): int;
}
