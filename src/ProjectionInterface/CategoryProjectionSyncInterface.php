<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ProjectionInterface;

/**
 * Defines the contract for category projection sync.
 */
interface CategoryProjectionSyncInterface
{
    /**
     * Apply domain event payloads to MySQL read models.
     *
     * @param array<string,mixed> $event
     */
    public function apply(array $event): void;
}
