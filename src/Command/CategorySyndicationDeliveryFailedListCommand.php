<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\RepositoryInterface\CategorySyndicationDeliveryRecordRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Executes the category syndication delivery failed list command console workflow.
 */
#[AsCommand(name: 'category:syndication:delivery:failed:list')]
final class CategorySyndicationDeliveryFailedListCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;
    /**
     * Initializes the category syndication delivery failed list command service collaborators.
     */
    public function __construct(private readonly CategorySyndicationDeliveryRecordRepositoryInterface $repository)
    {
        parent::__construct();
    }
    /**
     * Configures the command definition and available options.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('List failed syndication delivery records in json or ndjson format.')
            ->setHelp('Use this command to inspect failed syndication delivery records and print them in ndjson or json format.')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'ndjson');
    }
    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $format = $this->optionString($input, 'format', 'json');
        $payload = array_map(static fn ($record): array => [
            'deliveryId' => $record->deliveryId(),
            'packageId' => $record->packageId(),
            'destinationId' => $record->destinationId(),
            'categoryId' => $record->categoryId(),
            'status' => $record->status()->status(),
            'attempt' => $record->attempt(),
            'responseCode' => $record->responseCode(),
            'responseMessage' => $record->responseMessage(),
            'deliveredAt' => $record->deliveredAt()?->format(DATE_ATOM),
        ], $this->repository->failedRecords());

        if ('json' === $format) {
            $output->writeln(json_encode($payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            return Command::SUCCESS;
        }

        foreach ($payload as $item) {
            $output->writeln(json_encode($item, JSON_THROW_ON_ERROR));
        }

        return Command::SUCCESS;
    }
}
