<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:runtime:self-check')]
final class CategoryRuntimeSelfCheckCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = dirname(__DIR__, 2);

        $checks = [
            'openApiTreeRoute' => $this->contains($root.'/api/category-openapi.yaml', '/api/category/tree'),
            'adminMoveRouteYaml' => $this->contains($root.'/config/routes/category-move.yaml', 'admin_category_move')
                && $this->contains($root.'/config/routes/category-move.yaml', 'CategoryMoveController::__invoke'),
            'runtimeManifestCommand' => $this->contains($root.'/src/Command/CategoryRuntimeManifestCommand.php', 'category:runtime:self-check'),
            'runtimeProbeCommand' => $this->contains($root.'/src/Command/CategoryRuntimeProbeCommand.php', 'selfCheckCommand'),
            'runtimeGateCommand' => $this->contains($root.'/src/Command/CategoryRuntimeGateCommand.php', 'selfCheckCommand'),
            'runtimeReleaseReportCommand' => $this->contains($root.'/src/Command/CategoryRuntimeReleaseReportCommand.php', "#[AsCommand(name: 'category:runtime:release-report')]"),
            'runtimeRcVerdictCommand' => $this->contains($root.'/src/Command/CategoryRuntimeRcVerdictCommand.php', "#[AsCommand(name: 'category:runtime:rc-verdict')]"),
            'runtimeReleaseEnvelopeCommand' => $this->contains($root.'/src/Command/CategoryRuntimeReleaseEnvelopeCommand.php', "#[AsCommand(name: 'category:runtime:release-envelope')]"),
            'publicReadController' => $this->contains($root.'/src/Controller/CategoryApiController.php', "'ok' => true")
                && $this->contains($root.'/src/Controller/CategoryApiController.php', "'pageInfo'"),
        ];

        $passed = 0;
        foreach ($checks as $ok) {
            if (true === $ok) {
                ++$passed;
            }
        }

        $payload = [
            'ok' => true,
            'command' => 'category:runtime:self-check',
            'checks' => $checks,
            'passedCount' => $passed,
            'totalCount' => count($checks),
            'runtimeSurfaceHealthy' => $passed === count($checks),
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
