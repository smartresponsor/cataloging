<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CategoryPublicationQualityServiceInterface;
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

    public function __construct(private readonly CategoryPublicationQualityServiceInterface $qualityService)
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
        $publicationChecks = json_decode((string) $input->getOption('publication-checks'), true, 512, JSON_THROW_ON_ERROR);
        $checks = json_decode((string) $input->getOption('checks'), true, 512, JSON_THROW_ON_ERROR);

        $event = $this->qualityService->evaluate(
            (string) $input->getArgument('categoryId'),
            (int) $input->getArgument('score'),
            is_array($publicationChecks) ? $publicationChecks : [],
            is_array($checks) ? $checks : [],
            (string) $input->getArgument('actorId'),
            (string) $input->getArgument('reason'),
        );

        return $this->writeJson($output, $event->payload());
    }
}
