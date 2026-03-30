<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CatalogPublicationQualityServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:quality:evaluate')]
final class CategoryPublicationQualityEvaluateCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    public function __construct(private readonly CatalogPublicationQualityServiceInterface $qualityService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Evaluate category publication quality from CLI checks and score inputs.')
            ->setHelp('Use this command to calculate publication quality, blockers, and warnings for a category from CLI payloads.')
            ->addArgument('categoryId', InputArgument::REQUIRED)
            ->addArgument('score', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('publication-checks', null, InputOption::VALUE_REQUIRED)
            ->addOption('checks', null, InputOption::VALUE_REQUIRED, '', '{}');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $publicationChecks = $this->decodeJsonMapOption($input, 'publication-checks');
        $checks = $this->decodeJsonMapOption($input, 'checks');

        $event = $this->qualityService->evaluate(
            $this->argumentString($input, 'categoryId'),
            $this->argumentInt($input, 'score'),
            $publicationChecks,
            $checks,
            $this->argumentString($input, 'actorId'),
            $this->argumentString($input, 'reason'),
        );

        return $this->writeJson($output, $event->payload());
    }

    /** @return array<string,bool> */
    private function decodeJsonMapOption(InputInterface $input, string $name): array
    {
        $decoded = json_decode($this->optionString($input, $name, '{}'), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            return [];
        }

        $result = [];
        foreach ($decoded as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $result[$key] = (bool) $value;
        }

        return $result;
    }
}
