<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\Service\ProjectionRunner;
use App\Service\TreeConsistencyChecker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Executes the rebuild category tree command console workflow.
 */
#[AsCommand(name: 'category:rebuild-tree')]
final class RebuildCategoryTreeCommand extends Command
{
    use CategoryCliOutputTrait;
    /**
     * Initializes the rebuild category tree command service collaborators.
     */
    public function __construct(
        private readonly TreeConsistencyChecker $checker,
        private readonly ProjectionRunner $runner,
    ) {
        parent::__construct();
    }
    /**
     * Runs the command workflow and returns the process status.
     */
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
