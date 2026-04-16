<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\Service\ArrayValueNormalizer;
use App\ServiceInterface\CatalogSyndicationDestinationGovernanceSummaryServiceInterface;
use App\ValueObject\CategorySyndicationDestinationGovernanceSummaryRequest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the category syndication destination governance summary command console workflow.
 */
#[AsCommand(name: 'category:governance:summary:destination')]
final class CategorySyndicationDestinationGovernanceSummaryCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the category syndication destination governance summary command service collaborators.
     */
    public function __construct(
        private readonly CatalogSyndicationDestinationGovernanceSummaryServiceInterface $service,
        private readonly ArrayValueNormalizer $arrayValueNormalizer = new ArrayValueNormalizer(),
    ) {
        parent::__construct();
    }

    /**
     * Configures the command definition and available options.
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setDescription('Build a destination governance summary from governance trails.')
            ->setHelp(
                'Use this command to aggregate destination governance trails '
                .'and print a normalized governance summary.',
            )
            ->addArgument('destinationId', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('trails', null, InputOption::VALUE_REQUIRED, default: '[]')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'json');
    }

    /**
     * Runs the command workflow and returns the process status.
     */
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $event = $this->service->buildSummary(new CategorySyndicationDestinationGovernanceSummaryRequest(
                $this->argumentString($input, 'destinationId'),
                $this->decodeTrails($this->optionString($input, 'trails', '[]')),
                $this->argumentString($input, 'actorId'),
                $this->argumentString($input, 'reason'),
            ));

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
        } catch (\Throwable $exception) {
            $output->writeln((string) json_encode([
                'ok' => false,
                'error' => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::FAILURE;
        }
    }

    /**
     * @param string $json
     *
     * @return list<array<string,mixed>>
     *
     * @throws \JsonException
     */
    private function decodeTrails(string $json): array
    {
        return $this->arrayValueNormalizer->stringKeyedRowList(
            json_decode($json, true, 512, JSON_THROW_ON_ERROR),
        );
    }
}
