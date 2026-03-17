<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Command;

use App\Command\RunCategoryProjectionCommand;
use App\RunnerInterface\CategoryProjectionRunnerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RunCategoryProjectionCommandTest extends TestCase
{
    public function testOnceModeCapsLoopInputsAndWritesReport(): void
    {
        @unlink('report/category-projection-runner.json');

        $captured = [];
        $runner = new class($captured) implements CategoryProjectionRunnerInterface {
            public function __construct(private array &$captured)
            {
            }

            public function run(int $maxSec, int $maxBatch): void
            {
                $this->captured = [$maxSec, $maxBatch];
            }
        };

        $tester = new CommandTester(new RunCategoryProjectionCommand($runner));
        $tester->execute(['--once' => true, '--max-sec' => '10', '--max-batch' => '20']);

        self::assertSame([1, 1], $captured);
        self::assertFileExists('report/category-projection-runner.json');
    }
}
