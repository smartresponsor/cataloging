<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CategoryCompletenessServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:completeness:evaluate')]
final class CategoryCompletenessEvaluateCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(private readonly CategoryCompletenessServiceInterface $completenessService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Evaluate category completeness from a CLI payload.')
            ->setHelp('Use this command to pass completeness input payloads from the CLI and print the resulting report as JSON.')
            ->addArgument('categoryId', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED)
            ->addOption('payload', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $payload = json_decode((string) $input->getOption('payload'), true, 512, JSON_THROW_ON_ERROR);

        $event = $this->completenessService->evaluate(
            (string) $input->getArgument('categoryId'),
            is_array($payload) ? $payload : [],
            (string) $input->getArgument('actorId'),
            (string) $input->getArgument('reason'),
        );

        return $this->writeJson($output, $event->payload());
    }
}
