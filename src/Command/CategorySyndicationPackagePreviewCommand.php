<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CatalogSyndicationPackageGateServiceInterface;
use App\ValueObject\CatalogAuditContext;
use App\ValueObject\CategorySyndicationPackageBuildRequest;
use App\ValueObject\CategorySyndicationPackageContext;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the category syndication package preview command console workflow.
 */
#[AsCommand(name: 'category:syndication:package:preview')]
final class CategorySyndicationPackagePreviewCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the category syndication package preview command service collaborators.
     */
    public function __construct(private readonly CatalogSyndicationPackageGateServiceInterface $service)
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
            ->setDescription('Build and print a preview syndication package for a category and destination.')
            ->setHelp('Use this command to preview the mapped syndication package for a category before delivery.')
            ->addArgument('categoryId', InputArgument::REQUIRED)
            ->addOption('mapping', null, InputOption::VALUE_REQUIRED)
            ->addOption('destination', null, InputOption::VALUE_REQUIRED)
            ->addOption('actor-id', null, InputOption::VALUE_REQUIRED, default: 'cli')
            ->addOption('reason', null, InputOption::VALUE_REQUIRED, default: 'preview')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, default: 'json');
    }

    /**
     * Runs the command workflow and returns the process status.
     *
     * @throws \JsonException
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $categoryId = $this->argumentString($input, 'categoryId');
        $actorId = $this->optionString($input, 'actor-id', 'cli');
        $reason = $this->optionString($input, 'reason', 'preview');
        $format = $this->optionString($input, 'format', 'json');

        $mapping = $this->jsonOptionMap($input, 'mapping');
        $destinationSettings = $this->jsonOptionMap($input, 'destination');

        $destinationId = $this->nonEmptyString(
            $mapping['destinationId'] ?? $destinationSettings['destinationId'] ?? null,
            'cli-preview-destination',
        );
        $packageId = $this->nonEmptyString($mapping['packageId'] ?? null, 'cli-preview-package');
        $version = $this->nonEmptyString($mapping['version'] ?? null, '1');
        $localeMode = $this->nonEmptyString($mapping['localeMode'] ?? null, 'per_locale');

        $event = $this->service->buildGatedPublishPackage(
            new CategorySyndicationPackageBuildRequest(
                new CategorySyndicationPackageContext(
                    $packageId,
                    $destinationId,
                    $categoryId,
                    $version,
                    $localeMode,
                ),
                $this->nestedMap($mapping['payload'] ?? null),
                $this->stringMap($mapping['fieldMap'] ?? null),
                $this->stringList($mapping['requiredFields'] ?? null),
                new CatalogAuditContext($actorId, $reason),
            ),
        );
        $payload = $this->eventPayload($event);

        if ('ndjson' === $format) {
            return $this->writeStructuredRows($output, [$payload], 'ndjson');
        }

        return $this->writeJson($output, $payload);
    }

    /** @return array<string,mixed> */
    private function eventPayload(object $event): array
    {
        return method_exists($event, 'payload')
            ? $event->payload()
            : (method_exists($event, 'toArray') ? $event->toArray() : ['result' => 'ok']);
    }
}
