<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\Ops\CategoryRuntimeStatusViewBuilderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
/**
 * Executes the category runtime status command console workflow.
 */
#[AsCommand(name: 'category:runtime:status', description: 'Build category runtime status contour.')]
final class CategoryRuntimeStatusCommand extends Command
{
    /**
     * Initializes the category runtime status command service collaborators.
     */
    public function __construct(private readonly CategoryRuntimeStatusViewBuilderInterface $viewBuilder)
    {
        parent::__construct();
    }
    /**
     * Configures the command definition and available options.
     */
    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('categoryId', InputArgument::REQUIRED, 'Category id');
    }
    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $io = new SymfonyStyle($input, $output);
        $arg = $input->getArgument('categoryId');
        $categoryId = is_scalar($arg) ? (string) $arg : '';
        $view = $this->viewBuilder->build($categoryId)->toArray();
        $json = json_encode($view, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $io->writeln(false !== $json ? $json : '{}');

        return Command::SUCCESS;
    }
}
