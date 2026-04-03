<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-idempotency-readiness-report.json';

function fileContains(string $path, string $needle): bool
{
    return is_file($path) && str_contains((string) file_get_contents($path), $needle);
}

function fileText(string $path): string
{
    return is_file($path) ? (string) file_get_contents($path) : '';
}

$storeSource = $root . '/src/Idempotency/CategoryIdempotencyStore.php';
$storeInterfaceSource = $root . '/src/IdempotencyInterface/CategoryIdempotencyStoreInterface.php';
$mutationSource = $root . '/src/Service/CategoryMutationService.php';
$controllerSource = $root . '/src/Controller/CategoryApiController.php';
$commandSource = $root . '/src/Command/CategoryIdempotencyPurgeCommand.php';
$migrationSource = $root . '/migrations/Version20251102075000_category_idempotency_runtime_hardening.php';
$docsSource = $root . '/docs/category-idempotency-readiness.md';
$composerSource = $root . '/composer.json';

$mutationText = fileText($mutationSource);
$controllerText = fileText($controllerSource);
$storeText = fileText($storeSource);

$items = [
    [
        'check' => 'db-backed-store',
        'status' => fileContains($storeSource, 'Doctrine\\DBAL\\Connection') && fileContains($storeSource, 'category_idempotency') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Idempotency/CategoryIdempotencyStore.php'],
    ],
    [
        'check' => 'interface-acquire-contract',
        'status' => fileContains($storeInterfaceSource, 'acquire(') && fileContains($storeInterfaceSource, 'purgeExpired(') ? 'pass' : 'fail',
        'details' => ['file' => 'src/IdempotencyInterface/CategoryIdempotencyStoreInterface.php'],
    ],
    [
        'check' => 'payload-mismatch-protection',
        'status' => str_contains($storeText, 'request_hash') && str_contains($storeText, 'different request payload') ? 'pass' : 'warn',
        'details' => ['file' => 'src/Idempotency/CategoryIdempotencyStore.php'],
    ],
    [
        'check' => 'mutation-move-uses-idempotency',
        'status' => str_contains($mutationText, 'category.move') && str_contains($mutationText, 'duplicateMoveResult') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CategoryMutationService.php'],
    ],
    [
        'check' => 'mutation-publish-uses-idempotency',
        'status' => str_contains($mutationText, 'category.publish') && str_contains($mutationText, 'duplicatePublishResult') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CategoryMutationService.php'],
    ],
    [
        'check' => 'request-header-support',
        'status' => str_contains($controllerText, 'X-Idempotency-Key') && str_contains($controllerText, 'X-Correlation-ID') ? 'pass' : 'warn',
        'details' => ['file' => 'src/Controller/CategoryApiController.php'],
    ],
    [
        'check' => 'idempotency-migration',
        'status' => fileContains($migrationSource, 'CREATE TABLE category_idempotency') && fileContains($migrationSource, 'request_hash') ? 'pass' : 'fail',
        'details' => ['file' => 'migrations/Version20251102075000_category_idempotency_runtime_hardening.php'],
    ],
    [
        'check' => 'purge-command',
        'status' => fileContains($commandSource, 'app:category:idempotency:purge') ? 'pass' : 'warn',
        'details' => ['file' => 'src/Command/CategoryIdempotencyPurgeCommand.php'],
    ],
    [
        'check' => 'idempotency-doc',
        'status' => is_file($docsSource) ? 'pass' : 'warn',
        'details' => ['file' => 'docs/category-idempotency-readiness.md'],
    ],
    [
        'check' => 'composer-report-wiring',
        'status' => str_contains(fileText($composerSource), 'report:idempotency-readiness') ? 'pass' : 'warn',
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
echo sprintf('[CatalogIdempotencyReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s' . PHP_EOL, $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
