<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CatalogWorkflowTransitionServiceInterface;
use App\ValueObject\CategoryWorkflowTransitionRequest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the category workflow transition command console workflow.
 */
#[AsCommand(name: 'category:workflow:transition')]
final class CategoryWorkflowTransitionCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the category workflow transition command service collaborators.
     */
    public function __construct(private readonly CatalogWorkflowTransitionServiceInterface $transitionService)
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
            ->setDescription('Execute a category workflow transition and print the transition payload.')
            ->setHelp(
                'Use this command to move a category workflow to a target state '
                .'and emit the transition payload as JSON.',
            )
            ->addArgument('categoryId', InputArgument::REQUIRED)
            ->addArgument('targetState', InputArgument::REQUIRED)
            ->addArgument('actorId', InputArgument::REQUIRED)
            ->addArgument('reason', InputArgument::REQUIRED);
    }

    /**
     * Runs the command workflow and returns the process status.
     *
     * @throws \JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $event = $this->transitionService->transition(new CategoryWorkflowTransitionRequest(
            $this->argumentString($input, 'categoryId'),
            $this->argumentString($input, 'targetState'),
            $this->argumentString($input, 'actorId'),
            $this->argumentString($input, 'reason'),
        ));

        return $this->writeJson($output, $event->payload());
    }
}
