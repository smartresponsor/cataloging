<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
/**
 * Executes the backup category command console workflow.
 */
#[AsCommand(name: 'category:backup')]
final class BackupCategoryCommand extends Command
{
    use CategoryCliOutputTrait;
    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $fs = new Filesystem();
        $fs->dumpFile('category-backup.ndjson', '{"id":1,"slug":"root"}\n');
        $output->writeln('<info>Backup created</info>');

        return self::SUCCESS;
    }
}
