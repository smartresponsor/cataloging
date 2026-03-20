<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 */

namespace App\Worker;

use App\RunnerInterface\CategoryProjectionRunnerInterface;

final class ProjectionSyncWorker
{
    public function __construct(
        private readonly ?CategoryProjectionRunnerInterface $runner = null,
    ) {
    }

    public function run(): void
    {
        if (null === $this->runner) {
            return;
        }

        $this->runner->run(1, 10);
    }
}
