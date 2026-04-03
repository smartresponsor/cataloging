<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\IdempotencyInterface;

interface CategoryIdempotencyStoreInterface
{
    /**
     * Attempts to reserve a command key for the given retention window.
     *
     * Returns false when the key is still active and the operation should be
     * treated as a duplicate. Implementations must reject re-use of the same
     * idempotency key for a different payload hash.
     */
    public function acquire(string $key, string $operation, string $requestHash, int $ttlSec, ?string $correlationId = null): bool;

    /** Removes expired keys and returns the number of purged rows. */
    public function purgeExpired(): int;
}
