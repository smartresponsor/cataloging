<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Idempotency;

use App\IdempotencyInterface\testsIdempotencyStoreInterface;

/** In-memory placeholder; real implementation in infra (Redis/DB). */
final class testsIdempotencyStore implements testsIdempotencyStoreInterface
{
    /** @var array<string,int> */
    private array $state = [];

    public function seen(string $key): bool
    {
        return array_key_exists($key, $this->state);
    }

    public function mark(string $key, int $ttlSec): void
    {
        $this->state[$key] = time() + $ttlSec;
    }
}
