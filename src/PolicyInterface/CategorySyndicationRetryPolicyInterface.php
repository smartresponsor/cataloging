<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\PolicyInterface;

interface CategorySyndicationRetryPolicyInterface
{
    public function isRetryable(?int $responseCode): bool;

    public function assertFailedStatus(string $status): void;

    public function nextAttempt(int $currentAttempt): int;

    public function delaySecondsForAttempt(int $nextAttempt): int;
}
