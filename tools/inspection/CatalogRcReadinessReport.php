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

$runtimeProof = readJsonOrEmpty($reportDir . '/catalog-runtime-proof-report.json');
$smokeProof = readJsonOrEmpty($reportDir . '/catalog-smoke-proof-report.json');
$routeInventory = readJsonOrEmpty($reportDir . '/catalog-route-inventory-report.json');
$dependencyBaseline = readJsonOrEmpty($reportDir . '/catalog-dependency-baseline-report.json');
$ownerOverlap = readJsonOrEmpty($reportDir . '/catalog-owner-overlap-report.json');
$classAlias = readJsonOrEmpty($reportDir . '/catalog-class-alias-report.json');

$gitStatus = commandResult('git -C ' . escapeshellarg($root) . ' status --porcelain');
$consoleAbout = commandResult('cd ' . escapeshellarg($root) . ' && APP_ENV=prod APP_DEBUG=0 php bin/console about --no-ansi');

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
        'status' => count($runtimeProof['items'] ?? []) >= 6 ? 'pass' : 'warn',
        'details' => ['itemCount' => count($runtimeProof['items'] ?? [])],
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
        'status' => (($routeInventory['count'] ?? 0) >= 10) ? 'pass' : 'warn',
        'details' => ['routeCount' => $routeInventory['count'] ?? 0],
    ],
    [
        'check' => 'bundle-loadability',
        'status' => (($dependencyBaseline['summary']['allBundlesLoadable'] ?? false) === true) ? 'pass' : 'fail',
        'details' => ['bundleLoadability' => $dependencyBaseline['bundleLoadability'] ?? []],
    ],
    [
        'check' => 'dependency-baseline-clean',
        'status' => (($dependencyBaseline['summary']['vendorDirty'] ?? true) === false && ($dependencyBaseline['lockedPackages']['missingDirectoriesCount'] ?? 1) === 0) ? 'pass' : 'warn',
        'details' => [
            'vendorDirty' => $dependencyBaseline['summary']['vendorDirty'] ?? null,
            'missingLockedDirectoriesCount' => $dependencyBaseline['lockedPackages']['missingDirectoriesCount'] ?? null,
        ],
    ],
    [
        'check' => 'phpunit-extension-readiness',
        'status' => $missingPhpUnitExtensions === [] ? 'pass' : 'warn',
        'details' => ['missingExtensions' => $missingPhpUnitExtensions],
    ],
    [
        'check' => 'owner-overlap-signals',
        'status' => (($ownerOverlap['count'] ?? 0) === 0) ? 'pass' : 'warn',
        'details' => ['count' => $ownerOverlap['count'] ?? 0],
    ],
    [
        'check' => 'class-alias-signals',
        'status' => (($classAlias['count'] ?? 0) === 0) ? 'pass' : 'warn',
        'details' => ['count' => $classAlias['count'] ?? 0],
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
