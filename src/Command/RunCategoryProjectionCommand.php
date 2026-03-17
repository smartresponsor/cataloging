<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use App\RunnerInterface\CategoryProjectionRunnerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:category:projection:run')]
final class RunCategoryProjectionCommand extends Command
{
    public function __construct(private readonly CategoryProjectionRunnerInterface $runner)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('once', null, InputOption::VALUE_NONE, 'Run a short single projection cycle.')
            ->addOption('max-sec', null, InputOption::VALUE_REQUIRED, 'Maximum run time in seconds.', '5')
            ->addOption('max-batch', null, InputOption::VALUE_REQUIRED, 'Maximum events to process.', '50');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $once = (bool) $input->getOption('once');
        $maxSec = max(1, (int) $input->getOption('max-sec'));
        $maxBatch = max(1, (int) $input->getOption('max-batch'));

        if ($once) {
            $maxSec = min($maxSec, 1);
            $maxBatch = min($maxBatch, 1);
        }

        $this->runner->run($maxSec, $maxBatch);

        $payload = [
            'command' => 'app:category:projection:run',
            'once' => $once,
            'maxSec' => $maxSec,
            'maxBatch' => $maxBatch,
            'status' => 'ok',
            'at' => gmdate(DATE_ATOM),
        ];

        @mkdir('report', 0o755, true);
        file_put_contents('report/category-projection-runner.json', json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $output->writeln('<info>category projection run completed</info>');

        return self::SUCCESS;
    }
}
