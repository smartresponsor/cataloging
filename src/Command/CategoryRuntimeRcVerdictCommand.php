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

#[AsCommand(name: 'category:runtime:rc-verdict')]
final class CategoryRuntimeRcVerdictCommand extends Command
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

        $publicRows = $this->repository->publishedTree($taxonomy, null, 50, $locale);
        $publicIds = array_values(array_map(static fn (array $row): string => (string) $row['id'], $publicRows));

        $projectRoot = dirname(__DIR__, 2);
        $checks = [
            'manifestKnowsRcVerdict' => $this->contains($projectRoot.'/src/Command/CategoryRuntimeManifestCommand.php', 'category:runtime:rc-verdict'),
            'probeKnowsRcVerdict' => $this->contains($projectRoot.'/src/Command/CategoryRuntimeProbeCommand.php', 'rcVerdictCommand'),
            'gateKnowsRcVerdict' => $this->contains($projectRoot.'/src/Command/CategoryRuntimeGateCommand.php', 'rcVerdictCommand'),
            'selfCheckKnowsRcVerdict' => $this->contains($projectRoot.'/src/Command/CategoryRuntimeSelfCheckCommand.php', 'runtimeRcVerdictCommand'),
            'releaseReportKnowsRcVerdict' => $this->contains($projectRoot.'/src/Command/CategoryRuntimeReleaseReportCommand.php', 'runtimeRcVerdictCommand'),
            'contractKnowsRcVerdict' => $this->contains($projectRoot.'/tests/Category/Api/CategoryContractTest.php', 'category:runtime:rc-verdict'),
            'regressionKnowsRcVerdict' => $this->contains($projectRoot.'/tests/Category/Regression/CategoryRegressionTest.php', 'category:runtime:rc-verdict'),
            'runtimeReleaseEnvelopeCommand' => $this->contains($projectRoot.'/src/Command/CategoryRuntimeReleaseEnvelopeCommand.php', "#[AsCommand(name: 'category:runtime:release-envelope')]"),
        ];

        $payload = [
            'ok' => true,
            'command' => 'category:runtime:rc-verdict',
            'taxonomy' => $taxonomy,
            'locale' => $locale,
            'stateLoaded' => $stateLoaded,
            'publicCount' => count($publicIds),
            'publicIds' => $publicIds,
            'checks' => $checks,
            'rcVerdict' => !in_array(false, $checks, true) && [] !== $publicIds,
            'verdict' => !in_array(false, $checks, true) && [] !== $publicIds ? 'rc-near' : 'not-ready',
            'nextLayer' => 'category:runtime:release-envelope',
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
