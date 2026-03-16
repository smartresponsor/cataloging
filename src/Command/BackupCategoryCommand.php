<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'category:backup')]
final class BackuptestsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();
        $fs->dumpFile('category-backup.ndjson', '{"id":1,"slug":"root"}\n');
        $output->writeln('<info>Backup created</info>');

        return self::SUCCESS;
    }
}
