<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Worker;

use App\RunnerInterface\CategoryProjectionRunnerInterface;
/**
 * Provides the projection sync worker implementation.
 */
final class ProjectionSyncWorker
{
    /**
     * Initializes the projection sync worker service collaborators.
     */
    public function __construct(
        private readonly ?CategoryProjectionRunnerInterface $runner = null,
    ) {
    }
    /**
     * Handles the run workflow.
     */
    public function run(): void
    {
        if (null === $this->runner) {
            return;
        }

        $this->runner->run(1, 10);
    }
}
