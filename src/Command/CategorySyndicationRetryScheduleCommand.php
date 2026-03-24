<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\RepositoryInterface\CategorySyndicationDeliveryRecordRepositoryInterface;
use App\ServiceInterface\CatalogSyndicationRetryServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:syndication:retry:schedule')]
final class CategorySyndicationRetryScheduleCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(
        private readonly CategorySyndicationDeliveryRecordRepositoryInterface $repository,
        private readonly CatalogSyndicationRetryServiceInterface $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Schedule a retry for a failed syndication delivery record.')
            ->setHelp('Use this command to prepare a retry plan for a failed syndication delivery and print the retry payload.')
            ->addArgument('deliveryId', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $deliveryId = (string) $input->getArgument('deliveryId');
        $actorId = (string) $input->getArgument('actorId');
        $reason = (string) $input->getArgument('reason');
        $format = (string) $input->getOption('format');

        $record = $this->repository->find($deliveryId);
        if (null === $record) {
            $output->writeln(json_encode(['error' => 'delivery_not_found', 'deliveryId' => $deliveryId], JSON_THROW_ON_ERROR));

            return Command::FAILURE;
        }

        $event = $this->service->scheduleRetry($record, $actorId, $reason);
        $payload = method_exists($event, 'payload') ? $event->payload() : (method_exists($event, 'toArray') ? $event->toArray() : ['result' => 'ok']);

        if ('ndjson' === $format) {
            return $this->writeStructuredRows($output, [$payload], 'ndjson');
        }

        return $this->writeJson($output, $payload);
    }
}
