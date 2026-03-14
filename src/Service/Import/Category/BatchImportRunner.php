<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Import\Category;

use App\Service\Workflow\Category\ProgressTracker;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class BatchImportRunner
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly ProgressTracker $progress,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    /** @param iterable<array<string,mixed>> $items */
    public function run(iterable $items, int $chunk = 100): void
    {
        $batch = [];
        foreach ($items as $item) {
            $batch[] = $item;
            if (count($batch) >= $chunk) {
                $this->flush($batch);
                $batch = [];
            }
        }

        if ([] !== $batch) {
            $this->flush($batch);
        }
    }

    /** @param array<int,array<string,mixed>> $batch */
    private function flush(array $batch): void
    {
        $ok = 0;
        $fail = 0;

        foreach ($batch as $index => $item) {
            try {
                ++$ok;
            } catch (\Throwable $throwable) {
                ++$fail;
                $this->logger->error('Category batch import item failed.', [
                    'index' => $index,
                    'item' => $item,
                    'exception' => $throwable,
                ]);
            }
        }

        $this->progress->report($ok, $fail);
    }
}
