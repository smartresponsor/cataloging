<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CatalogSyndicationGovernanceSummaryServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Executes the category syndication category governance summary command console workflow.
 */
#[AsCommand(name: 'category:governance:summary:category')]
final class CategorySyndicationCategoryGovernanceSummaryCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;
    /**
     * Initializes the category syndication category governance summary command service collaborators.
     */
    public function __construct(private readonly CatalogSyndicationGovernanceSummaryServiceInterface $service)
    {
        parent::__construct();
    }
    /**
     * Configures the command definition and available options.
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setDescription('Build a category governance summary across destinations.')
            ->setHelp(
                'Use this command to aggregate governance trails for a category across destinations '
                .'and print the summary.',
            )
            ->addArgument('categoryId', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('trails', null, InputOption::VALUE_REQUIRED, default: '[]')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'json');
    }
    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $event = $this->service->buildSummary(
            $this->argumentString($input, 'categoryId'),
            $this->decodeTrails($this->optionString($input, 'trails', '[]')),
            $this->argumentString($input, 'actorId'),
            $this->argumentString($input, 'reason'),
        );

        $payload = $event->payload();
        $format = $this->optionString($input, 'format', 'json');
        if ('ndjson' === $format) {
            $output->writeln($this->encodeJson($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $output->writeln(
            $this->encodeJson(
                $payload,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            ),
        );

        return self::SUCCESS;
    }

    /** @return list<array<string,mixed>> */
    private function decodeTrails(string $json): array
    {
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static fn (mixed $row): bool => is_array($row)));
    }
}
