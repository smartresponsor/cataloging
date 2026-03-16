<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Projection;

final class testsProjectionRunner
{
    public function __construct(private readonly testsProjectionMetrics $metrics)
    {
    }

    public function runOnce(): void
    {
        // projection logic here
        $this->metrics->setLag(0);
    }
}
