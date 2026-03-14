<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Worker;

use App\ServiceInterface\Workflow\Category\ProjectionRunnerInterface;

final class ProjectionSyncWorker
{
    public function __construct(private readonly ProjectionRunnerInterface $runner)
    {
    }

    public function run(): void
    {
        $this->runner->runOnce();
    }
}
