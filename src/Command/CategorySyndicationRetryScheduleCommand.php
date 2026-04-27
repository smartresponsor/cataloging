<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Command;

use App\Cataloging\RepositoryInterface\Catalog\CatalogSyndicationDeliveryRecordRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationRetryServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the category syndication retry schedule command console workflow.
 */
#[AsCommand(name: 'category:syndication:retry:schedule')]
final class CategorySyndicationRetryScheduleCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the category syndication retry schedule command service collaborators.
     */
    public function __construct(
        private readonly CatalogSyndicationDeliveryRecordRepositoryInterface $repository,
        private readonly CatalogSyndicationRetryServiceInterface $service,
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
            ->setDescription('Schedule a retry for a failed syndication delivery record.')
            ->setHelp(
                'Use this command to prepare a retry plan for a failed syndication delivery '
                .'and print the retry payload.',
            )
            ->addArgument('deliveryId', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'json');
    }

    /**
     * Runs the command workflow and returns the process status.
     *
     * @throws \JsonException
     * @throws \Throwable
     */
    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $deliveryId = $this->argumentString($input, 'deliveryId');
        $actorId = $this->argumentString($input, 'actorId');
        $reason = $this->argumentString($input, 'reason');
        $format = $this->optionString($input, 'format', 'json');

        $record = $this->repository->find($deliveryId);
        if (null === $record) {
            $output->writeln($this->encodeJson(['error' => 'delivery_not_found', 'deliveryId' => $deliveryId], 0));

            return Command::FAILURE;
        }

        $event = $this->service->scheduleRetry($record, $actorId, $reason);
        $payload = $this->eventPayload($event);

        if ('ndjson' === $format) {
            return $this->writeStructuredRows($output, [$payload], 'ndjson');
        }

        return $this->writeJson($output, $payload);
    }

    /** @return array<string,mixed> */
    private function eventPayload(object $event): array
    {
        if (method_exists($event, 'payload')) {
            return $this->nestedMap($event->payload());
        }

        if (method_exists($event, 'toArray')) {
            return $this->nestedMap($event->toArray());
        }

        return ['result' => 'ok'];
    }
}
