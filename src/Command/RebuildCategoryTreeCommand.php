<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use App\Service\ProjectionRunner;
use App\Service\TreeConsistencyChecker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:rebuild-tree')]
final class RebuildtestsTreeCommand extends Command
{
    public function __construct(
        private readonly TreeConsistencyChecker $checker,
        private readonly ProjectionRunner $runner,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nodes = [
            ['id' => 1, 'level' => 0],
            ['id' => 2, 'level' => 1, 'parent' => 1],
        ];
        $errors = $this->checker->check($nodes);
        $rebuilt = $this->runner->run($nodes);
        file_put_contents('report/category-consistency.json', json_encode([
            'errors' => $errors,
            'rebuilt' => $rebuilt,
        ], JSON_PRETTY_PRINT));
        $output->writeln('<info>category tree rebuilt</info>');

        return self::SUCCESS;
    }
}
