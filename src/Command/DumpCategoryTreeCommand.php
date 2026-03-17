<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use App\Infrastructure\CategoryRepositoryStateStore;
use App\Repository\CategoryRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:tree:dump')]
final class DumpCategoryTreeCommand extends Command
{
    public function __construct(
        private readonly CategoryRepository $repository,
        private readonly ?CategoryRepositoryStateStore $stateStore = null,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('taxonomy', InputArgument::OPTIONAL, 'Taxonomy id', 'catalog')
            ->addOption('locale', null, InputOption::VALUE_REQUIRED, 'Locale', 'en')
            ->addOption('depth', null, InputOption::VALUE_REQUIRED, 'Depth', '5')
            ->addOption('include-drafts', null, InputOption::VALUE_NONE, 'Include draft rows')
            ->addOption('state-file', null, InputOption::VALUE_REQUIRED, 'Persisted repository state file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $taxonomy = trim((string) $input->getArgument('taxonomy'));
        $locale = trim((string) $input->getOption('locale'));
        $depth = max(1, min(8, (int) $input->getOption('depth')));
        $includeDrafts = true === $input->getOption('include-drafts');
        $stateFile = $input->getOption('state-file');

        if (is_string($stateFile) && '' !== trim($stateFile) && null !== $this->stateStore) {
            $this->stateStore->load($this->repository, trim($stateFile));
        }

        $rows = $includeDrafts
            ? $this->repository->tree($taxonomy, null, $depth, $locale)
            : $this->repository->publishedTree($taxonomy, null, $depth, $locale);

        $payload = [
            'ok' => true,
            'taxonomy' => $taxonomy,
            'locale' => $locale,
            'count' => count($rows),
            'includeDrafts' => $includeDrafts,
            'ids' => array_values(array_map(static fn (array $row): string => (string) $row['id'], $rows)),
            'data' => array_values($rows),
        ];

        $output->writeln(json_encode($payload, JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }
}
