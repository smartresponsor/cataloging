<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\CatalogReviewAssignmentServiceInterface;
use App\ValueObject\CategoryReviewAssignmentRequest;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Executes the category review assign command console workflow.
 */
#[AsCommand(name: 'category:review:assign')]
final class CategoryReviewAssignCommand extends Command
{
    use CategoryCliOutputTrait;
    use CategoryCliInputTrait;

    /**
     * Initializes the category review assign command service collaborators.
     */
    public function __construct(private readonly CatalogReviewAssignmentServiceInterface $assignmentService)
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
            ->setDescription('Assign a review request to a reviewer and print the assignment payload.')
            ->setHelp(
                'Use this command to assign a change request for review, including priority and optional due date.',
            )
            ->addArgument('requestId', InputArgument::REQUIRED)
            ->addArgument('reviewer', InputArgument::REQUIRED)
            ->addArgument('assignedBy', InputArgument::REQUIRED)
            ->addOption('priority', null, InputOption::VALUE_REQUIRED, '', 'normal')
            ->addOption('due-at', null, InputOption::VALUE_REQUIRED);
    }

    /**
     * Runs the command workflow and returns the process status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $dueAt = $this->optionString($input, 'due-at');
            $event = $this->assignmentService->assign(new CategoryReviewAssignmentRequest(
                $this->argumentString($input, 'requestId'),
                $this->argumentString($input, 'reviewer'),
                $this->argumentString($input, 'assignedBy'),
                $this->optionString($input, 'priority', 'normal'),
                '' !== $dueAt ? new \DateTimeImmutable($dueAt) : null,
            ));

            return $this->writeJson($output, $event->payload());
        } catch (\Throwable $exception) {
            $output->writeln((string) json_encode([
                'ok' => false,
                'error' => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::FAILURE;
        }
    }
}
