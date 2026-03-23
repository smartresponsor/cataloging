<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\IdempotencyInterface;

interface CategoryIdempotencyStoreInterface
{
    /** True if operation with given key has already been applied. */
    public function seen(string $key): bool;

    /** Mark operation as applied with retention window. */
    public function mark(string $key, int $ttlSec): void;
}
