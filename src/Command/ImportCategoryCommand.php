<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:import')]
final class ImportCategoryCommand extends Command
{
    use CategoryCliOutputTrait;

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'NDJSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        foreach (file($file) as $line) {
            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            // persist here
        }
        $output->writeln('<info>Import done</info>');

        return self::SUCCESS;
    }
}
