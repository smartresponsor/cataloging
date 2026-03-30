<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\OutboxInterface;

interface CategoryOutboxRetryInterface
{
    /**
     * Schedule retry for failed event with exponential backoff.
     *
     * @param array<string, mixed> $event
     */
    public function schedule(array $event, int $attempt): void;
}
