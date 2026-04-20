<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the batch import runner application service.
 */
final class BatchImportRunner
{
    private ProgressTracker $progress;

    /**
     * Initializes the batch import runner service collaborators.
     */
    public function __construct(ProgressTracker $progress)
    {
        $this->progress = $progress;
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
        if ($batch) {
            $this->flush($batch);
        }
    }

    /** @param array<int,array<string,mixed>> $batch */
    private function flush(array $batch): void
    {
        $successfulCount = 0;
        $failedCount = 0;
        foreach ($batch as $item) {
            try {
                $this->processItem($item);
                ++$successfulCount;
            } catch (\RuntimeException|\InvalidArgumentException|\TypeError $e) {
                ++$failedCount;
                error_log('[BatchImportRunner] '.$e->getMessage());
            }
        }
        $this->progress->report($successfulCount, $failedCount);
    }

    /** @param array<string,mixed> $item */
    private function processItem(array $item): void
    {
        if ([] === $item) {
            throw new \InvalidArgumentException('Batch item cannot be empty');
        }
    }
}
