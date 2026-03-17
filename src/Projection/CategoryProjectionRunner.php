<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Projection;

use App\Observability\CatalogProjectionMetrics;

final class CategoryProjectionRunner
{
    public function __construct(private readonly CatalogProjectionMetrics $metrics)
    {
    }

    public function runOnce(): void
    {
        // projection logic here
        $this->metrics->setLag(0);
    }
}
