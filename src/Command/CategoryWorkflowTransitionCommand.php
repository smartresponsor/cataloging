<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use App\ServiceInterface\CategoryWorkflowTransitionServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:workflow:transition')]
final class CategoryWorkflowTransitionCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(private readonly CategoryWorkflowTransitionServiceInterface $transitionService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Execute a category workflow transition and print the transition payload.')
            ->setHelp('Use this command to move a category workflow to a target state and emit the transition payload as JSON.')
            ->addArgument('categoryId', InputArgument::REQUIRED)
            ->addArgument('targetState', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $event = $this->transitionService->transition(
            (string) $input->getArgument('categoryId'),
            (string) $input->getArgument('targetState'),
            (string) $input->getArgument('actorId'),
            (string) $input->getArgument('reason'),
        );

        return $this->writeJson($output, $event->payload());
    }
}
