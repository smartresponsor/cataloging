<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use App\Infrastructure\CategoryRepositoryStateStore;
use App\Repository\CategoryRepository;
use App\Service\CategoryMutationCoordinator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:publish')]
final class PublishCategoryCommand extends Command
{
    public function __construct(
        private readonly CategoryMutationCoordinator $mutationCoordinator,
        private readonly CategoryRepository $repository,
        private readonly ?CategoryRepositoryStateStore $stateStore = null,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'Category id')
            ->addOption('unpublish', null, InputOption::VALUE_NONE, 'Switch category back to draft state')
            ->addOption('actor', null, InputOption::VALUE_REQUIRED, 'Actor id', 'console')
            ->addOption('state-file', null, InputOption::VALUE_REQUIRED, 'Persisted repository state file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = trim((string) $input->getArgument('id'));
        $published = !$input->getOption('unpublish');
        $actor = (string) $input->getOption('actor');
        $stateFile = $input->getOption('state-file');

        if (is_string($stateFile) && '' !== trim($stateFile) && null !== $this->stateStore) {
            $this->stateStore->load($this->repository, trim($stateFile));
        }

        $result = $this->mutationCoordinator->publishMany([$id], $published, $actor);
        $successCount = count($result['success']);
        $deliveryCount = count($result['deliveries']);

        if (0 === $successCount) {
            $output->writeln(sprintf('<error>Mutation failed</error> id=%s action=%s', $id, $published ? 'publish' : 'unpublish'));

            return self::FAILURE;
        }

        $row = $this->repository->findById($id, 'en');
        $publicRows = $this->repository->publishedTree((string) ($row['taxonomyId'] ?? 'catalog'), null, 5, 'en');
        $publicIds = array_values(array_map(static fn (array $item): string => (string) $item['id'], $publicRows));

        $output->writeln(sprintf(
            '<info>Mutation done</info> id=%s action=%s deliveries=%d state=%s publicCount=%d',
            $id,
            $published ? 'publish' : 'unpublish',
            $deliveryCount,
            (string) ($row['meta']['state'] ?? 'unknown'),
            count($publicIds),
        ));
        $output->writeln('publicIds='.implode(',', $publicIds));

        if (is_string($stateFile) && '' !== trim($stateFile) && null !== $this->stateStore) {
            $saved = $this->stateStore->save($this->repository, trim($stateFile));
            $output->writeln(sprintf('stateFile=%s savedCount=%d', $saved['file'], $saved['count']));
        }

        return self::SUCCESS;
    }
}
