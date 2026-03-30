<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Outbox;

use App\OutboxInterface\CategoryOutboxRetryInterface;

final class CategoryOutboxRetry implements CategoryOutboxRetryInterface
{
    /** @var list<array{event:array<string, mixed>,attempt:int,runAt:int}> */
    private array $scheduled = [];

    /** @param array<string, mixed> $event */
    public function schedule(array $event, int $attempt): void
    {
        $normalizedAttempt = max(1, $attempt);
        $delaySeconds = min(300, 2 ** min($normalizedAttempt, 8));

        $this->scheduled[] = [
            'event' => $event,
            'attempt' => $normalizedAttempt,
            'runAt' => time() + $delaySeconds,
        ];
    }

    /** @return list<array{event:array<string, mixed>,attempt:int,runAt:int}> */
    public function scheduled(): array
    {
        return $this->scheduled;
    }
}
