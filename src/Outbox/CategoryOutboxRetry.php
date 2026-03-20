<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Outbox;

use App\OutboxInterface\CategoryOutboxRetryInterface;

final class CategoryOutboxRetry implements CategoryOutboxRetryInterface
{
    /** @var list<array{event:array,attempt:int,runAt:int}> */
    private array $scheduled = [];

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

    /** @return list<array{event:array,attempt:int,runAt:int}> */
    public function scheduled(): array
    {
        return $this->scheduled;
    }
}
