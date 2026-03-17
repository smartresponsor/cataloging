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

#[AsCommand(name: 'category:runtime:closure')]
final class CategoryRuntimeClosureCommand extends Command
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
            ->addOption('state-file', null, InputOption::VALUE_REQUIRED, 'Persisted repository state file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $taxonomy = trim((string) $input->getArgument('taxonomy'));
        $locale = trim((string) $input->getOption('locale'));
        $stateFile = $input->getOption('state-file');

        $stateLoaded = false;
        if (is_string($stateFile) && '' !== trim($stateFile) && null !== $this->stateStore) {
            $loaded = $this->stateStore->load($this->repository, trim($stateFile));
            $stateLoaded = true === ($loaded['loaded'] ?? false);
        }

        $publicRows = $this->repository->publishedTree($taxonomy, null, 10, $locale);
        $publicIds = array_values(array_map(static fn (array $row): string => (string) $row['id'], $publicRows));

        $root = dirname(__DIR__, 2);
        $checks = [
            'treeOpenApi' => $this->contains($root.'/api/category-openapi.yaml', '/api/category/tree'),
            'adminMoveRouteYaml' => $this->contains($root.'/config/routes/category-move.yaml', 'admin_category_move')
                && $this->contains($root.'/config/routes/category-move.yaml', '/admin/category/tree/move')
                && $this->contains($root.'/config/routes/category-move.yaml', 'CategoryMoveController::__invoke'),
            'adminMoveController' => $this->contains($root.'/src/Controller/Admin/CategoryMoveController.php', 'public function __invoke(')
                && $this->contains($root.'/src/Controller/Admin/CategoryMoveController.php', 'return $this->move($body);'),
            'runtimeManifestCommand' => $this->contains($root.'/src/Command/CategoryRuntimeManifestCommand.php', 'category:runtime:closure'),
            'runtimeProbeCommand' => $this->contains($root.'/src/Command/CategoryRuntimeProbeCommand.php', 'closureCommand'),
            'publicReadController' => $this->contains($root.'/src/Controller/CategoryApiController.php', "'ok' => true")
                && $this->contains($root.'/src/Controller/CategoryApiController.php', "'pageInfo'"),
        ];

        $payload = [
            'ok' => true,
            'taxonomy' => $taxonomy,
            'locale' => $locale,
            'stateLoaded' => $stateLoaded,
            'publicCount' => count($publicIds),
            'publicIds' => $publicIds,
            'checks' => $checks,
            'allChecksPassed' => !in_array(false, $checks, true),
        ];

        $output->writeln(json_encode($payload, JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }

    private function contains(string $file, string $needle): bool
    {
        if (!is_file($file)) {
            return false;
        }

        return str_contains((string) file_get_contents($file), $needle);
    }
}
