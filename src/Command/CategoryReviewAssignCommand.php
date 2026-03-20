<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Command;

use App\ServiceInterface\CategoryReviewAssignmentServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:review:assign')]
final class CategoryReviewAssignCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(private readonly CategoryReviewAssignmentServiceInterface $assignmentService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Assign a review request to a reviewer and print the assignment payload.')
            ->setHelp('Use this command to assign a change request for review, including priority and optional due date.')
            ->addArgument('requestId', InputArgument::REQUIRED)
            ->addArgument('reviewer', InputArgument::REQUIRED)
            ->addArgument('assignedBy', InputArgument::REQUIRED)
            ->addOption('priority', null, InputOption::VALUE_REQUIRED, '', 'normal')
            ->addOption('due-at', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dueAt = $input->getOption('due-at');
        $event = $this->assignmentService->assign(
            (string) $input->getArgument('requestId'),
            (string) $input->getArgument('reviewer'),
            (string) $input->getArgument('assignedBy'),
            (string) $input->getOption('priority'),
            is_string($dueAt) && '' !== $dueAt ? new \DateTimeImmutable($dueAt) : null,
        );

        return $this->writeJson($output, $event->payload());
    }
}
