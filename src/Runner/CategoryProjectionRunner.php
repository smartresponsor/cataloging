<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Runner;

use App\RunnerInterface\CategoryProjectionRunnerInterface;
use App\Service\ProjectionWorker;

final class CategoryProjectionRunner implements CategoryProjectionRunnerInterface
{
    public function __construct(private readonly ProjectionWorker $worker)
    {
    }

    public function run(int $maxSec, int $maxBatch): void
    {
        $startedAt = new \DateTimeImmutable('now');
        $processed = 0;
        $budgetSeconds = max(1, $maxSec);
        $batchLimit = max(1, $maxBatch);

        while ((new \DateTimeImmutable('now'))->getTimestamp() - $startedAt->getTimestamp() < $budgetSeconds && $processed < $batchLimit) {
            $remaining = $batchLimit - $processed;
            $stepLimit = min(50, $remaining);
            $handled = $this->worker->runOnce($stepLimit);
            if ($handled <= 0) {
                break;
            }

            $processed += $handled;
            if ($handled < $stepLimit) {
                break;
            }
        }
    }
}
