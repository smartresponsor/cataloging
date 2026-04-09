<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\RunnerInterface;
/**
 * Defines the contract for category projection runner.
 */
interface CategoryProjectionRunnerInterface
{
    /** Run sync loop with backoff; return when queue is drained or stop requested. */
    public function run(int $maxSec, int $maxBatch): void;
}
