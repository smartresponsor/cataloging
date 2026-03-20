<?php

declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CategorySyndicationPackageGateServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:syndication:package:preview')]
final class CategorySyndicationPackagePreviewCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(private readonly CategorySyndicationPackageGateServiceInterface $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Build and print a preview syndication package for a category and destination.')
            ->setHelp('Use this command to preview the mapped syndication package for a category before delivery.')
            ->addArgument('categoryId', InputArgument::REQUIRED)
            ->addOption('mapping', null, InputOption::VALUE_REQUIRED)
            ->addOption('destination', null, InputOption::VALUE_REQUIRED)
            ->addOption('actor-id', null, InputOption::VALUE_REQUIRED, default: 'cli')
            ->addOption('reason', null, InputOption::VALUE_REQUIRED, default: 'preview')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $categoryId = (string) $input->getArgument('categoryId');
        $actorId = (string) $input->getOption('actor-id');
        $reason = (string) $input->getOption('reason');
        $format = (string) $input->getOption('format');

        $mapping = json_decode((string) ($input->getOption('mapping') ?? '{}'), true, 512, JSON_THROW_ON_ERROR);
        $destinationSettings = json_decode((string) ($input->getOption('destination') ?? '{}'), true, 512, JSON_THROW_ON_ERROR);

        $destinationId = is_string($destinationSettings['destinationId'] ?? null) && '' !== trim((string) $destinationSettings['destinationId'])
            ? trim((string) $destinationSettings['destinationId'])
            : 'cli-preview-destination';

        $packageId = is_string($mapping['packageId'] ?? null) && '' !== trim((string) $mapping['packageId'])
            ? trim((string) $mapping['packageId'])
            : 'cli-preview-package';

        $version = is_scalar($mapping['version'] ?? null) ? trim((string) $mapping['version']) : '1';
        $localeMode = is_string($mapping['localeMode'] ?? null) && '' !== trim((string) $mapping['localeMode'])
            ? trim((string) $mapping['localeMode'])
            : 'per_locale';

        $event = $this->service->buildGatedPublishPackage(
            $packageId,
            $destinationId,
            $categoryId,
            $version,
            $localeMode,
            is_array($mapping['payload'] ?? null) ? $mapping['payload'] : [],
            is_array($mapping['fieldMap'] ?? null) ? $mapping['fieldMap'] : [],
            is_array($mapping['requiredFields'] ?? null) ? $mapping['requiredFields'] : [],
            $actorId,
            $reason,
        );
        $payload = method_exists($event, 'payload') ? $event->payload() : (method_exists($event, 'toArray') ? $event->toArray() : ['result' => 'ok']);

        if ('ndjson' === $format) {
            return $this->writeStructuredRows($output, [$payload], 'ndjson');
        }

        return $this->writeJson($output, $payload);
    }
}
