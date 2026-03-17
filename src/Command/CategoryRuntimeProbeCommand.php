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

#[AsCommand(name: 'category:runtime:probe')]
final class CategoryRuntimeProbeCommand extends Command
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

        $publicRows = $this->repository->publishedTree($taxonomy, null, 5, $locale);
        $allRows = $this->repository->tree($taxonomy, null, 5, $locale);
        $publicIds = array_values(array_map(static fn (array $row): string => (string) $row['id'], $publicRows));
        $draftIds = [];
        foreach ($allRows as $row) {
            if (true !== (bool) (($row['meta']['published'] ?? false) === true)) {
                $draftIds[] = (string) $row['id'];
            }
        }

        $root = dirname(__DIR__, 2);
        $runtimeFiles = [
            'src/Controller/CategoryApiController.php',
            'src/Controller/Api/CategoryAdminApiController.php',
            'src/Command/PublishCategoryCommand.php',
            'src/Command/DumpCategoryTreeCommand.php',
            'src/Command/CategoryRuntimeManifestCommand.php',
            'src/Command/CategoryRuntimeClosureCommand.php',
            'src/Command/CategoryRuntimeSelfCheckCommand.php',
            'src/Command/CategoryRuntimeReleaseReportCommand.php',
            'src/Command/CategoryRuntimeRcVerdictCommand.php',
            'src/Command/CategoryRuntimeReleaseEnvelopeCommand.php',
            'api/category-openapi.yaml',
            'config/graphql/category.yaml',
        ];

        $markers = [
            'treeRoute' => $this->contains($root.'/api/category-openapi.yaml', '/api/category/tree'),
            'bulkAdminSurface' => $this->contains($root.'/src/Controller/Api/CategoryAdminApiController.php', 'deliveryCount'),
            'adminMoveRoute' => $this->contains($root.'/config/routes/category-move.yaml', '/admin/category/tree/move') && $this->contains($root.'/config/routes/category-move.yaml', 'CategoryMoveController::__invoke'),
            'publishCommand' => $this->contains($root.'/src/Command/PublishCategoryCommand.php', "#[AsCommand(name: 'category:publish')]"),
            'dumpCommand' => $this->contains($root.'/src/Command/DumpCategoryTreeCommand.php', "#[AsCommand(name: 'category:tree:dump')]"),
            'closureCommand' => $this->contains($root.'/src/Command/CategoryRuntimeClosureCommand.php', "#[AsCommand(name: 'category:runtime:closure')]"),
            'gateCommand' => $this->contains($root.'/src/Command/CategoryRuntimeGateCommand.php', "#[AsCommand(name: 'category:runtime:gate')]"),
            'selfCheckCommand' => $this->contains($root.'/src/Command/CategoryRuntimeSelfCheckCommand.php', "#[AsCommand(name: 'category:runtime:self-check')]"),
            'releaseReportCommand' => $this->contains($root.'/src/Command/CategoryRuntimeReleaseReportCommand.php', "#[AsCommand(name: 'category:runtime:release-report')]"),
            'rcVerdictCommand' => $this->contains($root.'/src/Command/CategoryRuntimeRcVerdictCommand.php', "#[AsCommand(name: 'category:runtime:rc-verdict')]"),
            'releaseEnvelopeCommand' => $this->contains($root.'/src/Command/CategoryRuntimeReleaseEnvelopeCommand.php', "#[AsCommand(name: 'category:runtime:release-envelope')]"),
        ];

        $payload = [
            'ok' => true,
            'taxonomy' => $taxonomy,
            'locale' => $locale,
            'stateLoaded' => $stateLoaded,
            'publicCount' => count($publicIds),
            'draftCount' => count($draftIds),
            'publicIds' => $publicIds,
            'draftIds' => $draftIds,
            'runtimeFilesPresent' => $this->allFilesPresent($root, $runtimeFiles),
            'markers' => $markers,
        ];

        $output->writeln(json_encode($payload, JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }

    /** @param list<string> $files */
    private function allFilesPresent(string $root, array $files): bool
    {
        foreach ($files as $file) {
            if (!is_file($root.'/'.$file)) {
                return false;
            }
        }

        return true;
    }

    private function contains(string $file, string $needle): bool
    {
        if (!is_file($file)) {
            return false;
        }

        return str_contains((string) file_get_contents($file), $needle);
    }
}
