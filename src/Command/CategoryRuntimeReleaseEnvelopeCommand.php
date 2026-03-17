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

#[AsCommand(name: 'category:runtime:release-envelope')]
final class CategoryRuntimeReleaseEnvelopeCommand extends Command
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
            'manifestKnowsReleaseEnvelope' => $this->contains($root.'/src/Command/CategoryRuntimeManifestCommand.php', 'category:runtime:release-envelope'),
            'probeKnowsReleaseEnvelope' => $this->contains($root.'/src/Command/CategoryRuntimeProbeCommand.php', 'releaseEnvelopeCommand'),
            'gateKnowsReleaseEnvelope' => $this->contains($root.'/src/Command/CategoryRuntimeGateCommand.php', 'releaseEnvelopeCommand'),
            'selfCheckKnowsReleaseEnvelope' => $this->contains($root.'/src/Command/CategoryRuntimeSelfCheckCommand.php', 'runtimeReleaseEnvelopeCommand'),
            'releaseReportKnowsReleaseEnvelope' => $this->contains($root.'/src/Command/CategoryRuntimeReleaseReportCommand.php', 'runtimeReleaseEnvelopeCommand'),
            'rcVerdictKnowsReleaseEnvelope' => $this->contains($root.'/src/Command/CategoryRuntimeRcVerdictCommand.php', 'nextLayer'),
        ];

        $ready = !in_array(false, $checks, true) && $stateLoaded && [] !== $publicIds;
        $payload = [
            'ok' => true,
            'command' => 'category:runtime:release-envelope',
            'stateLoaded' => $stateLoaded,
            'publicIds' => $publicIds,
            'checks' => $checks,
            'handoffReady' => $ready,
            'verdict' => $ready ? 'release-envelope-ready' : 'not-ready',
            'releaseEnvelopeReady' => $ready,
            'releaseEnvelopePassed' => $ready,
            'runtimeReleaseEnvelopeReady' => $ready,
            'runtimeReleaseEnvelopePassed' => $ready,
            'releaseReady' => $ready,
            'finalReady' => $ready,
        ];
        $output->writeln(json_encode($payload, JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }

    private function contains(string $file, string $needle): bool
    {
        return is_file($file) && str_contains((string) file_get_contents($file), $needle);
    }
}
