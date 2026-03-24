<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
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

#[AsCommand(name: 'category:syndication:history:destination')]
final class CategorySyndicationDestinationHistoryCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(private readonly CatalogSyndicationHistoryServiceInterface $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Build destination history from delivery records and print the result.')
            ->setHelp('Use this command to aggregate destination delivery history from CLI-supplied records in json or ndjson format.')
            ->addArgument('destinationId', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('records', null, InputOption::VALUE_REQUIRED, default: '[]')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $destinationId = (string) $input->getArgument('destinationId');
        $actorId = (string) $input->getArgument('actorId');
        $reason = (string) $input->getArgument('reason');
        $format = (string) $input->getOption('format');
        $decoded = json_decode((string) $input->getOption('records'), true, 512, JSON_THROW_ON_ERROR);
        $records = [];
        foreach (is_array($decoded) ? $decoded : [] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $records[] = new CategorySyndicationDeliveryRecord(
                (string) ($row['deliveryId'] ?? ''),
                (string) ($row['packageId'] ?? ''),
                (string) ($row['destinationId'] ?? ''),
                (string) ($row['categoryId'] ?? ''),
                new CategorySyndicationDeliveryStatus((string) ($row['status'] ?? 'pending')),
                (int) ($row['attempt'] ?? 1),
                isset($row['responseCode']) ? (int) $row['responseCode'] : null,
                (string) ($row['responseMessage'] ?? ''),
                isset($row['deliveredAt']) && is_string($row['deliveredAt']) && '' !== $row['deliveredAt'] ? new \DateTimeImmutable($row['deliveredAt']) : null,
            );
        }

        $event = $this->service->buildDestinationHistory($destinationId, $records, $actorId, $reason);
        $payload = method_exists($event, 'payload') ? $event->payload() : (method_exists($event, 'toArray') ? $event->toArray() : ['result' => 'ok']);

        if ('ndjson' === $format) {
            return $this->writeStructuredRows($output, [$payload], 'ndjson');
        }

        return $this->writeJson($output, $payload);
    }
}
