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

#[AsCommand(name: 'category:export')]
final class ExportCategoryCommand extends Command
{
    use CategoryCliOutputTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows = [
            ['id' => '1', 'slug' => 'root'],
        ];
        foreach ($rows as $row) {
            $output->writeln(json_encode($row, JSON_UNESCAPED_UNICODE));
        }

        return self::SUCCESS;
    }
}
