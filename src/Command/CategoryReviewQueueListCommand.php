<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use App\ServiceInterface\CategoryReviewQueueServiceInterface;
use App\ValueObject\CategoryReviewQueueItem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:review:queue:list')]
final class CategoryReviewQueueListCommand extends Command
{
    use CategoryCliOutputTrait;

    public function __construct(private readonly CategoryReviewQueueServiceInterface $queueService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('List the review queue for a reviewer in json or ndjson format.')
            ->setHelp('Use this command to inspect review queue items for a reviewer and print them in ndjson or json format.')
            ->addArgument('reviewer', InputArgument::REQUIRED)
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'ndjson|json', 'ndjson');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reviewer = (string) $input->getArgument('reviewer');
        $format = (string) $input->getOption('format');
        $items = $this->queueService->queueForReviewer($reviewer);

        $payload = array_map(static fn (CategoryReviewQueueItem $item): array => self::normalizeItem($item), $items);

        return $this->writeStructuredRows($output, $payload, $format);
    }

    /** @return array<string,mixed> */
    private static function normalizeItem(CategoryReviewQueueItem $item): array
    {
        return [
            'requestId' => $item->requestId(),
            'categoryId' => $item->categoryId(),
            'assignedReviewer' => $item->assignedReviewer(),
            'priority' => $item->priority(),
            'requestState' => $item->requestState(),
            'readyForReview' => $item->readyForReview(),
            'readinessWarnings' => $item->readinessWarnings(),
            'dueAt' => $item->dueAt()?->format(DATE_ATOM),
        ];
    }
}
