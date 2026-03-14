<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Tests\Worker;

use App\ServiceInterface\Workflow\Category\ProjectionRunnerInterface;
use App\Worker\ProjectionSyncWorker;
use PHPUnit\Framework\TestCase;

final class CatalogProjectionSyncWorkerTest extends TestCase
{
    public function testRunDelegatesToRunner(): void
    {
        $runner = new class implements ProjectionRunnerInterface {
            public int $count = 0;

            public function runOnce(): void
            {
                ++$this->count;
            }

            public function lag(): int
            {
                return 0;
            }
        };

        $worker = new ProjectionSyncWorker($runner);
        $worker->run();

        self::assertSame(1, $runner->count);
    }
}
