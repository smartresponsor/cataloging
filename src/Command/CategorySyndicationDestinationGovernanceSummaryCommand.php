<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CatalogSyndicationDestinationGovernanceSummaryServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:governance:summary:destination')]
final class CategorySyndicationDestinationGovernanceSummaryCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(private readonly CatalogSyndicationDestinationGovernanceSummaryServiceInterface $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Build a destination governance summary from governance trails.')
            ->setHelp('Use this command to aggregate destination governance trails and print a normalized governance summary.')
            ->addArgument('destinationId', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('trails', null, InputOption::VALUE_REQUIRED, default: '[]')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $event = $this->service->buildSummary(
            (string) $input->getArgument('destinationId'),
            $this->decodeTrails((string) $input->getOption('trails')),
            (string) $input->getArgument('actorId'),
            (string) $input->getArgument('reason'),
        );

        $payload = $event->payload();
        $format = (string) $input->getOption('format');
        if ('ndjson' === $format) {
            $output->writeln(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $output->writeln(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }

    /** @return array<int,array<string,mixed>> */
    private function decodeTrails(string $json): array
    {
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static fn (mixed $row): bool => is_array($row)));
    }
}
