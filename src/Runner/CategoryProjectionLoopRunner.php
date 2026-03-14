<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Runner;

use App\Projection\CatalogProjectionRunner as CanonProjectionRunner;
use App\RunnerInterface\CategoryProjectionLoopRunnerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class CategoryProjectionLoopRunner implements CategoryProjectionLoopRunnerInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly CanonProjectionRunner $runner,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function run(int $maxSec, int $maxBatch): void
    {
        $startedAt = time();
        $cycles = 0;

        while ((time() - $startedAt) < $maxSec && $cycles < $maxBatch) {
            try {
                $this->runner->runOnce();
            } catch (\Throwable $throwable) {
                $this->logger->error('Legacy category projection runner loop failed.', [
                    'cycle' => $cycles,
                    'maxSec' => $maxSec,
                    'maxBatch' => $maxBatch,
                    'exception' => $throwable,
                ]);

                break;
            }

            ++$cycles;

            if (0 === $this->runner->lag()) {
                break;
            }

            usleep(100000);
        }
    }
}
