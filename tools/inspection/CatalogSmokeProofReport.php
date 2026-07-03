<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-smoke-proof-report.json';

/**
 * @return array{exitCode:int,output:list<string>}
 */
function runCommand(string $command): array
{
    $output = [];
    exec($command . ' 2>&1', $output, $exitCode);

    return ['exitCode' => $exitCode, 'output' => $output];
}

/**
 * @return array{check:string,command:string,status:string,exitCode:int,output:list<string>}
 */
function smokeCheck(string $label, string $command): array
{
    $result = runCommand($command);

    return [
        'check' => $label,
        'command' => $command,
        'status' => $result['exitCode'] === 0 ? 'pass' : 'fail',
        'exitCode' => $result['exitCode'],
        'output' => $result['output'],
    ];
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

$php = escapeshellarg(PHP_BINARY);
$runner = escapeshellarg($root . '/tools/php/php84.php');

$generatedArtifacts = [];

putenv('APP_ENV=prod');
putenv('APP_DEBUG=0');
$checks = [
    'container-boot-smoke' => sprintf('%s %s %s', $php, $runner, escapeshellarg($root . '/tools/smoke/category-container-boot-smoke.php')),
    'runtime-smoke' => sprintf('%s %s %s', $php, $runner, escapeshellarg($root . '/tools/smoke/category-runtime-smoke.php')),
    'route-discovery-smoke' => sprintf('%s %s %s', $php, $runner, escapeshellarg($root . '/tools/smoke/category-route-discovery-smoke.php')),
    'doctrine-mapping-smoke' => sprintf('%s %s %s', $php, $runner, escapeshellarg($root . '/tools/smoke/category-doctrine-mapping-smoke.php')),
    'fixture-sanity-smoke' => sprintf('%s %s %s', $php, $runner, escapeshellarg($root . '/tools/smoke/category-fixture-sanity.php')),
    'fixture-load-smoke' => sprintf('%s %s %s', $php, $runner, escapeshellarg($root . '/tools/smoke/category-fixture-load-smoke.php')),
    'prod-console-about' => 'cd ' . escapeshellarg($root) . ' && ' . PHP_BINARY . ' -d variables_order=EGPCS bin/console about --no-ansi',
];

$items = [];
$summary = ['pass' => 0, 'fail' => 0];
foreach ($checks as $label => $command) {
    $item = smokeCheck($label, $command);
    $items[] = $item;
    ++$summary[$item['status']];
}

restoreGeneratedArtifacts($root, $generatedArtifacts);

$report = [
    'generatedAt' => date(DATE_ATOM),
    'overallStatus' => $summary['fail'] > 0 ? 'fail' : 'pass',
    'summary' => $summary,
    'items' => $items,
];

file_put_contents($out, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
printf(
    "[CatalogSmokeProofReport] status=%s pass=%d fail=%d written to %s\n",
    $report['overallStatus'],
    $summary['pass'],
    $summary['fail'],
    str_replace($root . DIRECTORY_SEPARATOR, '', $out)
);
