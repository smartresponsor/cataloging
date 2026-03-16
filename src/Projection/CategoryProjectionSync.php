<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Projection;

use App\ProjectionInterface\testsProjectionSyncInterface;

/**
 * Projection sync worker that updates MySQL read models from outbox events.
 * Real DB connections are injected in infrastructure layer.
 */
final class testsProjectionSync implements testsProjectionSyncInterface
{
    public function apply(array $event): void
    {
        // Upsert into category_flat / category_link_flat based on event type.
        // Implement SQL in infra; here we keep contract and method signature.
    }
}
