<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Worker;

use App\Cataloging\RunnerInterface\CategoryProjectionRunnerInterface;

/**
 * Provides the projection sync worker implementation.
 */
final readonly class ProjectionSyncWorker
{
    /**
     * Initializes the projection sync worker service collaborators.
     */
    public function __construct(
        private ?CategoryProjectionRunnerInterface $runner = null,
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
