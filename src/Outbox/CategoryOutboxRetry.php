<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Outbox;

use App\Cataloging\OutboxInterface\CategoryOutboxRetryInterface;

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

    public function nextRunAt(\DateTimeImmutable $now, int $attempt): \DateTimeImmutable
    {
        $delaySeconds = min(300, 2 ** min(max(1, $attempt), 8));

        return $now->modify(sprintf('+%d seconds', $delaySeconds));
    }

    /** @return list<array{event:array<string, mixed>,attempt:int,runAt:int}> */
    public function scheduled(): array
    {
        return $this->scheduled;
    }
}
