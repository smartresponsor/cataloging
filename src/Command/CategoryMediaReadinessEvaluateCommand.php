<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CatalogDestinationMediaReadinessServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:media:readiness:evaluate')]
final class CategoryMediaReadinessEvaluateCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(private readonly CatalogDestinationMediaReadinessServiceInterface $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Evaluate destination-aware category media readiness from CLI inputs.')
            ->setHelp('Use this command to inspect category media readiness for a destination context and print the report in json or ndjson.')
            ->addArgument('categoryId', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('destination-id', null, InputOption::VALUE_REQUIRED, default: 'cli-preview-destination')
            ->addOption('destination', null, InputOption::VALUE_REQUIRED)
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $categoryId = (string) $input->getArgument('categoryId');
        $actorId = (string) $input->getArgument('actorId');
        $reason = (string) $input->getArgument('reason');
        $destinationJson = $input->getOption('destination');
        $destinationId = (string) $input->getOption('destination-id');
        $format = (string) $input->getOption('format');

        $settings = [];
        if (is_string($destinationJson) && '' !== $destinationJson) {
            $decoded = json_decode($destinationJson, true, 512, JSON_THROW_ON_ERROR);
            $settings = is_array($decoded) ? $decoded : [];
        }

        if ('cli-preview-destination' === $destinationId && isset($settings['destinationId']) && is_string($settings['destinationId']) && '' !== trim($settings['destinationId'])) {
            $destinationId = trim($settings['destinationId']);
        }

        $report = $this->service->evaluate($destinationId, $categoryId, $actorId, $reason);
        $payload = method_exists($report, 'payload') ? $report->payload() : (method_exists($report, 'toArray') ? $report->toArray() : ['result' => 'ok']);

        if ('ndjson' === $format) {
            return $this->writeStructuredRows($output, [$payload], 'ndjson');
        }

        return $this->writeJson($output, $payload);
    }
}
