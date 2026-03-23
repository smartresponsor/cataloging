<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Idempotency;

use App\IdempotencyInterface\CategoryIdempotencyStoreInterface;

/**
 * Process-local fallback implementation.
 *
 * Keeps short-lived idempotency marks when no external store is wired.
 * It is intentionally simple but no longer behaves as an unbounded placeholder.
 */
final class CategoryIdempotencyStore implements CategoryIdempotencyStoreInterface
{
    /** @var array<string,int> */
    private array $state = [];

    public function seen(string $key): bool
    {
        $this->purgeExpired();

        return array_key_exists($key, $this->state);
    }

    public function mark(string $key, int $ttlSec): void
    {
        $expiresAt = max(time() + $ttlSec, time() + 1);
        $this->state[$key] = $expiresAt;
    }

    private function purgeExpired(): void
    {
        $now = time();
        foreach ($this->state as $key => $expiresAt) {
            if ($expiresAt <= $now) {
                unset($this->state[$key]);
            }
        }
    }
}