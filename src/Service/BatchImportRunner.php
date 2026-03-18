<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class BatchImportRunner
{
    private ProgressTracker $progress;

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
        $ok = 0;
        $fail = 0;
        foreach ($batch as $item) {
            try {
                $this->processItem($item);
                ++$ok;
            } catch (\RuntimeException|\InvalidArgumentException|\TypeError $e) {
                ++$fail;
            }
        }
        $this->progress->report($ok, $fail);
    }

    /** @param array<string,mixed> $item */
    private function processItem(array $item): void
    {
        if ([] === $item) {
            throw new \InvalidArgumentException('Batch item cannot be empty');
        }

        // Upsert hook point. Domain-specific adapters may replace this runner.
    }
}
