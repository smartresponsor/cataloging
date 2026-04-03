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
        $runAt = $this->nextRunAt(new \DateTimeImmutable('now'), $normalizedAttempt);

        $this->scheduled[] = [
            'event' => $event,
            'attempt' => $normalizedAttempt,
            'runAt' => $runAt->getTimestamp(),
        ];
    }

    public function nextDelaySeconds(int $attempt): int
    {
        $normalizedAttempt = max(1, $attempt);

        return min(900, 2 ** min($normalizedAttempt, 9));
    }

    public function nextRunAt(\DateTimeImmutable $now, int $attempt): \DateTimeImmutable
    {
        return $now->modify(sprintf('+%d seconds', $this->nextDelaySeconds($attempt)));
    }

    /** @return list<array{event:array<string, mixed>,attempt:int,runAt:int}> */
    public function scheduled(): array
    {
        return $this->scheduled;
    }
}
