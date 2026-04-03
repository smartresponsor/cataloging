<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-rc-readiness-report.json';

/**
 * @return array<string, mixed>
 */
function readJsonOrEmpty(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $decoded = json_decode((string) file_get_contents($path), true);

    return is_array($decoded) ? $decoded : [];
}

/**
 * @return array{exitCode:int,output:list<string>}
 */
function commandResult(string $command): array
{
    $output = [];
    exec($command . ' 2>&1', $output, $exitCode);

    return ['exitCode' => $exitCode, 'output' => $output];
}


/**
 * @param list<string> $paths
 */
function restoreGeneratedArtifacts(string $root, array $paths): void
{
    foreach ($paths as $path) {
        $fullPath = $root . '/' . ltrim($path, '/');
        if (!file_exists($fullPath)) {
            continue;
        }

        exec('git -C ' . escapeshellarg($root) . ' checkout -- ' . escapeshellarg($path));
    }
}

/**
 * @param array<string,mixed> $source
 * @return list<mixed>
 */
function listValue(array $source, string $key): array
{
    $value = $source[$key] ?? null;

    return is_array($value) ? array_values($value) : [];
}

/**
 * @param array<string,mixed> $source
 * @return array<string,mixed>
 */
function mapValue(array $source, string $key): array
{
    $value = $source[$key] ?? null;

    return is_array($value) ? $value : [];
}

/** @param array<string,mixed> $source */
function intValue(array $source, string $key, int $default = 0): int
{
    $value = $source[$key] ?? null;

    return is_int($value) ? $value : (is_numeric($value) ? (int) $value : $default);
}

/** @param array<string,mixed> $source */
function boolValue(array $source, string $key, bool $default = false): bool
{
    $value = $source[$key] ?? null;

    return is_bool($value) ? $value : $default;
}


$runtimeProof = readJsonOrEmpty($reportDir . '/catalog-runtime-proof-report.json');
$smokeProof = readJsonOrEmpty($reportDir . '/catalog-smoke-proof-report.json');
$routeInventory = readJsonOrEmpty($reportDir . '/catalog-route-inventory-report.json');
$dependencyBaseline = readJsonOrEmpty($reportDir . '/catalog-dependency-baseline-report.json');
$ownerOverlap = readJsonOrEmpty($reportDir . '/catalog-owner-overlap-report.json');
$classAlias = readJsonOrEmpty($reportDir . '/catalog-class-alias-report.json');
$migrationReadiness = readJsonOrEmpty($reportDir . '/catalog-migration-readiness-report.json');
$apiContractReadiness = readJsonOrEmpty($reportDir . '/catalog-api-contract-readiness-report.json');
$securityReadiness = readJsonOrEmpty($reportDir . '/catalog-security-readiness-report.json');
$oidcRuntimeProof = readJsonOrEmpty($reportDir . '/catalog-oidc-runtime-proof-report.json');
$outboxProjectionReadiness = readJsonOrEmpty($reportDir . '/catalog-outbox-projection-readiness-report.json');
$idempotencyReadiness = readJsonOrEmpty($reportDir . '/catalog-idempotency-readiness-report.json');
$searchReadiness = readJsonOrEmpty($reportDir . '/catalog-search-readiness-report.json');
$externalBoundaryReadiness = readJsonOrEmpty($reportDir . '/catalog-external-boundary-readiness-report.json');

$generatedArtifacts = ['config/reference.php'];

$gitStatus = commandResult('git -C ' . escapeshellarg($root) . ' status --porcelain');
$consoleAbout = commandResult('cd ' . escapeshellarg($root) . ' && APP_ENV=prod APP_DEBUG=0 php bin/console about --no-ansi');
restoreGeneratedArtifacts($root, $generatedArtifacts);

$requiredPhpUnitExtensions = ['dom', 'json', 'libxml', 'mbstring', 'tokenizer', 'xml', 'xmlwriter'];
$missingPhpUnitExtensions = [];
foreach ($requiredPhpUnitExtensions as $extension) {
    if (!extension_loaded($extension)) {
        $missingPhpUnitExtensions[] = $extension;
    }
}

$items = [
    [
        'check' => 'git-clean',
        'status' => $gitStatus['exitCode'] === 0 && count($gitStatus['output']) === 0 ? 'pass' : 'fail',
        'details' => ['dirtyEntries' => count($gitStatus['output'])],
    ],
    [
        'check' => 'prod-console-about',
        'status' => $consoleAbout['exitCode'] === 0 ? 'pass' : 'fail',
        'details' => ['exitCode' => $consoleAbout['exitCode']],
    ],
    [
        'check' => 'runtime-proof-report',
        'status' => count(listValue($runtimeProof, 'items')) >= 6 ? 'pass' : 'warn',
        'details' => ['itemCount' => count(listValue($runtimeProof, 'items'))],
    ],
    [
        'check' => 'smoke-proof-report',
        'status' => (($smokeProof['overallStatus'] ?? 'warn') === 'pass') ? 'pass' : 'warn',
        'details' => [
            'summary' => $smokeProof['summary'] ?? [],
        ],
    ],
    [
        'check' => 'route-inventory-report',
        'status' => (intValue($routeInventory, 'count') >= 10) ? 'pass' : 'warn',
        'details' => ['routeCount' => intValue($routeInventory, 'count')],
    ],
    [
        'check' => 'bundle-loadability',
        'status' => (boolValue(mapValue($dependencyBaseline, 'summary'), 'allBundlesLoadable') === true) ? 'pass' : 'fail',
        'details' => ['bundleLoadability' => listValue($dependencyBaseline, 'bundleLoadability')],
    ],
    [
        'check' => 'dependency-baseline-clean',
        'status' => (boolValue(mapValue($dependencyBaseline, 'summary'), 'vendorDirty', true) === false && intValue(mapValue($dependencyBaseline, 'lockedPackages'), 'missingDirectoriesCount', 1) === 0) ? 'pass' : 'warn',
        'details' => [
            'vendorDirty' => boolValue(mapValue($dependencyBaseline, 'summary'), 'vendorDirty', true),
            'missingLockedDirectoriesCount' => intValue(mapValue($dependencyBaseline, 'lockedPackages'), 'missingDirectoriesCount', 1),
        ],
    ],
    [
        'check' => 'phpunit-extension-readiness',
        'status' => $missingPhpUnitExtensions === [] ? 'pass' : 'warn',
        'details' => ['missingExtensions' => $missingPhpUnitExtensions],
    ],
    [
        'check' => 'owner-overlap-signals',
        'status' => (intValue($ownerOverlap, 'count') === 0) ? 'pass' : 'warn',
        'details' => ['count' => intValue($ownerOverlap, 'count')],
    ],
    [
        'check' => 'class-alias-signals',
        'status' => (intValue($classAlias, 'count') === 0) ? 'pass' : 'warn',
        'details' => ['count' => intValue($classAlias, 'count')],
    ],
    [
        'check' => 'migration-readiness',
        'status' => (($migrationReadiness['overallStatus'] ?? 'warn') === 'pass') ? 'pass' : 'warn',
        'details' => [
            'duplicateCreateCount' => intValue(mapValue($migrationReadiness, 'summary'), 'duplicateCreateCount'),
            'nonCanonicalVersionCount' => intValue(mapValue($migrationReadiness, 'summary'), 'nonCanonicalVersionCount'),
            'zeroDowntimeReady' => boolValue(mapValue($migrationReadiness, 'summary'), 'zeroDowntimeReady'),
        ],
    ],
    [
        'check' => 'api-contract-readiness',
        'status' => (($apiContractReadiness['overallStatus'] ?? 'warn') === 'pass') ? 'pass' : 'warn',
        'details' => [
            'documentedPathCount' => intValue(mapValue(mapValue($apiContractReadiness, 'openapi'), 'canonical'), 'pathCount'),
            'apiContractSummary' => mapValue($apiContractReadiness, 'summary'),
        ],
    ],
    [
        'check' => 'security-readiness',
        'status' => (($securityReadiness['overallStatus'] ?? 'warn') === 'pass') ? 'pass' : 'warn',
        'details' => [
            'securitySummary' => mapValue($securityReadiness, 'summary'),
        ],
    ],
    [
        'check' => 'oidc-runtime-proof',
        'status' => (($oidcRuntimeProof['overallStatus'] ?? 'warn') === 'pass') ? 'pass' : 'warn',
        'details' => [
            'oidcSummary' => mapValue($oidcRuntimeProof, 'summary'),
        ],
    ],
    [
        'check' => 'outbox-projection-readiness',
        'status' => (($outboxProjectionReadiness['overallStatus'] ?? 'warn') === 'pass') ? 'pass' : 'warn',
        'details' => [
            'outboxProjectionSummary' => mapValue($outboxProjectionReadiness, 'summary'),
        ],
    ],
    [
        'check' => 'idempotency-readiness',
        'status' => (($idempotencyReadiness['overallStatus'] ?? 'warn') === 'pass') ? 'pass' : 'warn',
        'details' => [
            'idempotencySummary' => mapValue($idempotencyReadiness, 'summary'),
        ],
    ],
    [
        'check' => 'search-readiness',
        'status' => (($searchReadiness['overallStatus'] ?? 'warn') === 'pass') ? 'pass' : 'warn',
        'details' => [
            'searchSummary' => mapValue($searchReadiness, 'summary'),
        ],
    ],

    [
        'check' => 'external-boundary-readiness',
        'status' => (($externalBoundaryReadiness['overallStatus'] ?? 'warn') === 'pass') ? 'pass' : 'warn',
        'details' => [
            'externalBoundarySummary' => mapValue($externalBoundaryReadiness, 'summary'),
        ],
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
echo sprintf('[CatalogRcReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s' . PHP_EOL, $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
