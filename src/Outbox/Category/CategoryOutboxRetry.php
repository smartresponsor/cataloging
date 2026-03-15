<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Outbox\Category;

final class CategoryOutboxRetry
{
    public function retry(array $event): void
    {
        // real impl in infra
    }
}
