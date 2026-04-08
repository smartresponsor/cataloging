<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Executes the export category command console workflow.
 */
#[AsCommand(name: 'category:export')]
final class ExportCategoryCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;
    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows = [
            ['id' => '1', 'slug' => 'root'],
        ];
        foreach ($rows as $row) {
            $json = json_encode($row, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $output->writeln($json);
        }

        return self::SUCCESS;
    }
}
