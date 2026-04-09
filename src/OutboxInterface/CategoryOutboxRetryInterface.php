<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\OutboxInterface;

/**
 * Defines the contract for category outbox retry.
 */
interface CategoryOutboxRetryInterface
{
    /**
     * Schedule retry for failed event with exponential backoff.
     *
     * @param array<string, mixed> $event
     */
    public function schedule(array $event, int $attempt): void;

    /**
     * Handles the next delay seconds workflow.
     */
    public function nextDelaySeconds(int $attempt): int;

    /**
     * Handles the next run at workflow.
     */
    public function nextRunAt(\DateTimeImmutable $now, int $attempt): \DateTimeImmutable;

    /** @return list<array{event:array<string, mixed>,attempt:int,runAt:int}> */
    public function scheduled(): array;
}
