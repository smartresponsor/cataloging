<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-outbox-projection-readiness-report.json';

function fileContains(string $path, string $needle): bool
{
    return is_file($path) && str_contains((string) file_get_contents($path), $needle);
}

function fileText(string $path): string
{
    return is_file($path) ? (string) file_get_contents($path) : '';
}

$workerSource = $root . '/src/Service/CatalogProjectionWorkerService.php';
$syncSource = $root . '/src/Projection/CategoryProjectionSync.php';
$commandSource = $root . '/src/Command/CategoryProjectionRunCommand.php';
$retrySource = $root . '/src/Outbox/CategoryOutboxRetry.php';
$migrationSource = $root . '/migrations/Version20251102073500_outbox_projection_runtime_hardening.php';
$infraSchemaSource = $root . '/config/sql/catalog_mysql_infra_category.sql';
$docsSource = $root . '/docs/category-outbox-projection-readiness.md';
$composerSource = $root . '/composer.json';

$syncText = fileText($syncSource);
$infraSchemaText = fileText($infraSchemaSource);

$items = [
    [
        'check' => 'outbox-worker-runtime-fields',
        'status' => fileContains($workerSource, 'available_at') && fileContains($workerSource, 'dead_lettered_at') && fileContains($workerSource, 'attempts') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CatalogProjectionWorkerService.php'],
    ],
    [
        'check' => 'outbox-worker-retry-backoff',
        'status' => fileContains($workerSource, 'nextRunAt') && fileContains($retrySource, 'nextDelaySeconds') ? 'pass' : 'warn',
        'details' => ['workerFile' => 'src/Service/CatalogProjectionWorkerService.php', 'retryFile' => 'src/Outbox/CategoryOutboxRetry.php'],
    ],
    [
        'check' => 'outbox-worker-dead-letter',
        'status' => fileContains($workerSource, 'dead_lettered_at') ? 'pass' : 'warn',
        'details' => ['file' => 'src/Service/CatalogProjectionWorkerService.php'],
    ],
    [
        'check' => 'projection-sync-real-upsert',
        'status' => str_contains($syncText, 'INSERT INTO category_projection') && str_contains($syncText, 'ON DUPLICATE KEY UPDATE') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Projection/CategoryProjectionSync.php'],
    ],
    [
        'check' => 'projection-sync-supported-events',
        'status' => str_contains($syncText, 'category.moved') && str_contains($syncText, 'category.published') && str_contains($syncText, 'category.unpublished') ? 'pass' : 'warn',
        'details' => ['file' => 'src/Projection/CategoryProjectionSync.php'],
    ],
    [
        'check' => 'projection-run-command',
        'status' => fileContains($commandSource, 'app:category:projection:run') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Command/CategoryProjectionRunCommand.php'],
    ],
    [
        'check' => 'outbox-hardening-migration',
        'status' => fileContains($migrationSource, 'available_at') && fileContains($migrationSource, 'dead_lettered_at') ? 'pass' : 'fail',
        'details' => ['file' => 'migrations/Version20251102073500_outbox_projection_runtime_hardening.php'],
    ],
    [
        'check' => 'infra-projection-schema-canonical-fields',
        'status' => str_contains($infraSchemaText, 'name varchar') && str_contains($infraSchemaText, 'path varchar') && str_contains($infraSchemaText, 'workflow_state') && str_contains($infraSchemaText, 'updated_at') ? 'pass' : 'warn',
        'details' => ['file' => 'config/sql/catalog_mysql_infra_category.sql'],
    ],
    [
        'check' => 'outbox-projection-doc',
        'status' => is_file($docsSource) ? 'pass' : 'warn',
        'details' => ['file' => 'docs/category-outbox-projection-readiness.md'],
    ],
    [
        'check' => 'composer-report-wiring',
        'status' => str_contains(fileText($composerSource), 'report:outbox-projection-readiness') ? 'pass' : 'warn',
        'details' => ['file' => 'composer.json'],
    ],
];

$summary = ['pass' => 0, 'warn' => 0, 'fail' => 0];
foreach ($items as $item) {
    ++$summary[$item['status']];
}
$overallStatus = $summary['fail'] > 0 ? 'fail' : ($summary['warn'] > 0 ? 'warn' : 'pass');

$report = [
    'generatedAt' => date(DATE_ATOM),
    'overallStatus' => $overallStatus,
    'summary' => $summary,
    'items' => $items,
];

file_put_contents($out, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo sprintf('[CatalogOutboxProjectionReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s' . PHP_EOL, $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
