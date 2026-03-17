<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use App\ImporterInterface\CategoryNdjsonImporterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:import')]
final class ImportCategoryCommand extends Command
{
    public function __construct(private readonly CategoryNdjsonImporterInterface $importer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'NDJSON file')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Validate only, do not persist rows');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->importer->import((string) $input->getArgument('file'), (bool) $input->getOption('dry-run'));

        $output->writeln(sprintf(
            '<info>Import done</info> ok=%d fail=%d warnings=%d',
            $result['ok'],
            $result['fail'],
            $result['warnings'],
        ));

        foreach ($result['report'] as $line) {
            $output->writeln($line);
        }

        return $result['fail'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
