<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Runner;

use App\Cataloging\RunnerInterface\CategoryProjectionRunnerInterface;
use App\Cataloging\Service\CatalogProjectionWorkerService;

/**
 * Provides the category projection runner implementation.
 */
final readonly class CategoryProjectionRunner implements CategoryProjectionRunnerInterface
{
    /**
     * Initializes the category projection runner service collaborators.
     */
    public function __construct(private CatalogProjectionWorkerService $worker)
    {
    }

    /**
     * Handles the run workflow.
     *
     * @throws \Throwable
     */
    public function run(int $maxSec, int $maxBatch): void
    {
        $startedAt = new \DateTimeImmutable('now');
        $processed = 0;
        $budgetSeconds = max(1, $maxSec);
        $batchLimit = max(1, $maxBatch);

        while ($processed < $batchLimit) {
            $currentAt = new \DateTimeImmutable('now');
            if ($currentAt->getTimestamp() - $startedAt->getTimestamp() >= $budgetSeconds) {
                break;
            }
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
