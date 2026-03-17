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

#[AsCommand(name: 'category:runtime:gate')]
final class CategoryRuntimeGateCommand extends Command
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

        $publicIds = array_values(array_map(
            static fn (array $row): string => (string) $row['id'],
            $this->repository->publishedTree($taxonomy, null, 50, $locale)
        ));

        $root = dirname(__DIR__, 2);
        $checks = [
            'manifestCommand' => is_file($root.'/src/Command/CategoryRuntimeManifestCommand.php'),
            'probeCommand' => is_file($root.'/src/Command/CategoryRuntimeProbeCommand.php'),
            'closureCommand' => is_file($root.'/src/Command/CategoryRuntimeClosureCommand.php'),
            'gateCommand' => is_file($root.'/src/Command/CategoryRuntimeGateCommand.php'),
            'selfCheckCommand' => is_file($root.'/src/Command/CategoryRuntimeSelfCheckCommand.php'),
            'releaseReportCommand' => is_file($root.'/src/Command/CategoryRuntimeReleaseReportCommand.php'),
            'rcVerdictCommand' => is_file($root.'/src/Command/CategoryRuntimeRcVerdictCommand.php'),
            'releaseEnvelopeCommand' => is_file($root.'/src/Command/CategoryRuntimeReleaseEnvelopeCommand.php'),
            'moveRouteClosure' => $this->contains($root.'/config/routes/category-move.yaml', 'CategoryMoveController::__invoke'),
            'publicTreeRoute' => $this->contains($root.'/api/category-openapi.yaml', '/api/category/tree'),
            'publicReadController' => is_file($root.'/src/Controller/CategoryApiController.php'),
        ];

        $runtimeGatePassed = !in_array(false, $checks, true) && $stateLoaded && [] !== $publicIds;

        $payload = [
            'ok' => true,
            'taxonomy' => $taxonomy,
            'locale' => $locale,
            'stateLoaded' => $stateLoaded,
            'publicCount' => count($publicIds),
            'publicIds' => $publicIds,
            'checks' => $checks,
            'runtimeGatePassed' => $runtimeGatePassed,
            'gateReady' => $runtimeGatePassed,
        ];

        $output->writeln(json_encode($payload, JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }

    private function contains(string $file, string $needle): bool
    {
        return is_file($file) && str_contains((string) file_get_contents($file), $needle);
    }
}
