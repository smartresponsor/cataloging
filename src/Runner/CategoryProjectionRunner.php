<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Runner;

use App\ProjectionInterface\testsProjectionSyncInterface;
use App\RunnerInterface\testsProjectionRunnerInterface;

final class testsProjectionRunner implements testsProjectionRunnerInterface
{
    private testsProjectionSyncInterface $sync;

    public function __construct(testsProjectionSyncInterface $sync)
    {
        $this->sync = $sync;
    }

    public function run(int $maxSec, int $maxBatch): void
    {
        $start = time();
        $processed = 0;
        while ((time() - $start) < $maxSec && $processed < $maxBatch) {
            // Infra should provide event fetch; here we call apply on sample structure.
            $this->sync->apply(['type' => 'noop']);
            ++$processed;
            usleep(100000); // 100ms backoff
        }
    }
}
