<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\Entity\CategorySyndicationDeliveryRecord;
use App\ServiceInterface\CatalogSyndicationHistoryServiceInterface;
use App\ValueObject\CategorySyndicationDeliveryStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the category syndication destination history command console workflow.
 */
#[AsCommand(name: 'category:syndication:history:destination')]
final class CategorySyndicationDestinationHistoryCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the category syndication destination history command service collaborators.
     */
    public function __construct(private readonly CatalogSyndicationHistoryServiceInterface $service)
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
            ->setDescription('Build destination history from delivery records and print the result.')
            ->setHelp(
                'Use this command to aggregate destination delivery history from CLI-supplied records '
                .'in json or ndjson format.',
            )
            ->addArgument('destinationId', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('records', null, InputOption::VALUE_REQUIRED, default: '[]')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'json');
    }

    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $destinationId = $this->argumentString($input, 'destinationId');
        $actorId = $this->argumentString($input, 'actorId');
        $reason = $this->argumentString($input, 'reason');
        $format = $this->optionString($input, 'format', 'json');
        $decoded = json_decode($this->optionString($input, 'records', '[]'), true, 512, JSON_THROW_ON_ERROR);
        $records = [];
        foreach (is_array($decoded) ? $decoded : [] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $records[] = new CategorySyndicationDeliveryRecord(
                $this->nonEmptyString($row['deliveryId'] ?? null),
                $this->nonEmptyString($row['packageId'] ?? null),
                $this->nonEmptyString($row['destinationId'] ?? null),
                $this->nonEmptyString($row['categoryId'] ?? null),
                new CategorySyndicationDeliveryStatus($this->nonEmptyString($row['status'] ?? 'pending', 'pending')),
                is_numeric($row['attempt'] ?? null) ? (int) $row['attempt'] : 1,
                isset($row['responseCode']) && is_numeric($row['responseCode']) ? (int) $row['responseCode'] : null,
                $this->nonEmptyString($row['responseMessage'] ?? null),
                isset($row['deliveredAt'])
                && is_string($row['deliveredAt'])
                && '' !== $row['deliveredAt']
                    ? new \DateTimeImmutable($row['deliveredAt'])
                    : null,
            );
        }

        $event = $this->service->buildDestinationHistory($destinationId, $records, $actorId, $reason);
        $payload = method_exists($event, 'payload')
            ? $event->payload()
            : (method_exists($event, 'toArray') ? $event->toArray() : ['result' => 'ok']);

        if ('ndjson' === $format) {
            return $this->writeStructuredRows($output, [$payload], 'ndjson');
        }

        return $this->writeJson($output, $payload);
    }
}
