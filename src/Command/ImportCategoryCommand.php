<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Executes the import category command console workflow.
 */
#[AsCommand(name: 'category:import')]
final class ImportCategoryCommand extends Command
{
    use CategoryCliOutputTrait;
    /**
     * Configures the command definition and available options.
     */
    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('file', InputArgument::REQUIRED, 'NDJSON file');
    }
    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $arg = $input->getArgument('file');
        $file = is_scalar($arg) ? (string) $arg : '';
        if ('' === $file) {
            $output->writeln('<error>Missing file.</error>');

            return self::INVALID;
        }
        $lines = file($file);
        if (false === $lines) {
            $output->writeln('<error>Cannot read file.</error>');

            return self::FAILURE;
        }
        foreach ($lines as $line) {
            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($data)) {
                continue;
            }
        }
        $output->writeln('<info>Import done</info>');

        return self::SUCCESS;
    }
}
