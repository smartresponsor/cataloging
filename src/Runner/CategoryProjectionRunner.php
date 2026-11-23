<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Runner;

use App\Cataloging\ProjectionInterface\CategoryProjectionSyncInterface;
use App\Cataloging\RunnerInterface\CategoryProjectionRunnerInterface;

final class CategoryProjectionRunner implements CategoryProjectionRunnerInterface
{
    private CategoryProjectionSyncInterface $sync;

    public function __construct(CategoryProjectionSyncInterface $sync)
    {
        $this->sync = $sync;
    }

    public function run(int $maxSec, int $maxBatch): void
    {
        $start = time();
        $processed = 0;

        while ((time() - $start) < $maxSec && $processed < $maxBatch) {
            $this->sync->apply([
                'type' => 'projection.tick',
                'sequence' => $processed + 1,
            ]);
            ++$processed;
            usleep(100000);
        }
    }
}
