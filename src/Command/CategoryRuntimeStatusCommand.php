<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Command;

use App\Service\Ops\CategoryRuntimeStatusViewBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'category:runtime:status', description: 'Build category runtime status contour.')]
final class CategoryRuntimeStatusCommand extends Command
{
    public function __construct(private readonly CategoryRuntimeStatusViewBuilder $viewBuilder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('categoryId', InputArgument::REQUIRED, 'Category id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $view = $this->viewBuilder->build((string) $input->getArgument('categoryId'))->toArray();

        $io->writeln(json_encode($view, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}');

        return Command::SUCCESS;
    }
}
