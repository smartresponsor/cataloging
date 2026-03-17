<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use App\ExporterInterface\CategoryNdjsonExporterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:export')]
final class ExportCategoryCommand extends Command
{
    public function __construct(private readonly CategoryNdjsonExporterInterface $exporter)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('taxonomy', InputArgument::REQUIRED, 'Taxonomy code')
            ->addArgument('file', InputArgument::REQUIRED, 'Destination NDJSON file')
            ->addArgument('locale', InputArgument::OPTIONAL, 'Locale', 'en');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $taxonomy = (string) $input->getArgument('taxonomy');
        $file = (string) $input->getArgument('file');
        $locale = (string) $input->getArgument('locale');

        $this->exporter->export($taxonomy, $file, $locale);
        $output->writeln(sprintf('<info>Export done</info> taxonomy=%s file=%s locale=%s', $taxonomy, $file, $locale));

        return self::SUCCESS;
    }
}
