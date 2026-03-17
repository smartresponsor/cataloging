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
            'adminMoveRouteYaml' => $this->contains($root.'/config/routes/category-move.yaml', 'admin_category_move'),
            'runtimeManifestCommand' => is_file($root.'/src/Command/CategoryRuntimeManifestCommand.php'),
            'runtimeProbeCommand' => is_file($root.'/src/Command/CategoryRuntimeProbeCommand.php'),
            'runtimeGateCommand' => is_file($root.'/src/Command/CategoryRuntimeGateCommand.php'),
            'runtimeReleaseReportCommand' => is_file($root.'/src/Command/CategoryRuntimeReleaseReportCommand.php'),
            'runtimeRcVerdictCommand' => is_file($root.'/src/Command/CategoryRuntimeRcVerdictCommand.php'),
            'runtimeReleaseEnvelopeCommand' => is_file($root.'/src/Command/CategoryRuntimeReleaseEnvelopeCommand.php'),
            'publicReadController' => is_file($root.'/src/Controller/CategoryApiController.php'),
        ];

        $passedCount = count(array_filter($checks, static fn (bool $ok): bool => $ok));
        $healthy = $passedCount === count($checks);

        $payload = [
            'ok' => true,
            'command' => 'category:runtime:self-check',
            'checks' => $checks,
            'passedCount' => $passedCount,
            'totalCount' => count($checks),
            'runtimeSurfaceHealthy' => $healthy,
            'selfCheckHealthy' => $healthy,
        ];

        $output->writeln(json_encode($payload, JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }

    private function contains(string $file, string $needle): bool
    {
        return is_file($file) && str_contains((string) file_get_contents($file), $needle);
    }
}
