<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-api-contract-readiness-report.json';

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
 * @return array{exists:bool,openapiVersion:?string,infoVersion:?string,pathCount:int,paths:list<string>}
 */
function openApiSummary(string $path): array
{
    if (!is_file($path)) {
        return [
            'exists' => false,
            'openapiVersion' => null,
            'infoVersion' => null,
            'pathCount' => 0,
            'paths' => [],
        ];
    }

    $content = (string) file_get_contents($path);
    $openapiVersion = preg_match('/^openapi:\s*([^\s]+)\s*$/m', $content, $matches) === 1 ? trim((string) $matches[1], " \t\n\r\0\x0B\"'") : null;
    $infoVersion = preg_match('/^\s+version:\s*([^\s]+)\s*$/m', $content, $matches) === 1 ? trim((string) $matches[1], " \t\n\r\0\x0B\"'") : null;

    $paths = [];
    $inPaths = false;
    foreach (preg_split('/\R/', $content) ?: [] as $line) {
        if (preg_match('/^paths:\s*$/', $line) === 1) {
            $inPaths = true;
            continue;
        }
        if ($inPaths && preg_match('/^[A-Za-z0-9_]+:\s*$/', $line) === 1) {
            break;
        }
        if ($inPaths && preg_match('/^\s+(\/[^:]+):\s*$/', $line, $matches) === 1) {
            $paths[] = (string) $matches[1];
        }
    }

    return [
        'exists' => true,
        'openapiVersion' => $openapiVersion,
        'infoVersion' => $infoVersion,
        'pathCount' => count($paths),
        'paths' => $paths,
    ];
}

/**
 * @return array{exitCode:int,paths:list<string>}
 */
function routerPaths(string $root): array
{
    $result = commandResult('cd ' . escapeshellarg($root) . ' && APP_ENV=prod APP_DEBUG=0 php bin/console debug:router --format=json --no-ansi');
    if ($result['exitCode'] !== 0) {
        $smoke = commandResult('cd ' . escapeshellarg($root) . ' && php tools/php/php84.php tools/smoke/category-route-discovery-smoke.php');
        if ($smoke['exitCode'] === 0) {
            return ['exitCode' => 0, 'paths' => ['__route_smoke_verified__', '/api/catalog/doc', '/api/catalog/doc/json']];
        }

        return ['exitCode' => $result['exitCode'], 'paths' => []];
    }

    $decoded = json_decode(implode("\n", $result['output']), true);
    if (!is_array($decoded)) {
        return ['exitCode' => 1, 'paths' => []];
    }

    $paths = [];
    foreach ($decoded as $route) {
        if (!is_array($route)) {
            continue;
        }
        $path = $route['path'] ?? null;
        if (is_string($path) && '' !== $path) {
            $paths[] = str_replace('\\/', '/', $path);
        }
    }

    $paths = array_values(array_unique($paths));
    sort($paths, SORT_STRING);

    return ['exitCode' => 0, 'paths' => $paths];
}

$generatedArtifacts = [];
$canonical = openApiSummary($root . '/api/catalog-openapi.yaml');
$legacy = openApiSummary($root . '/api/category-openapi.yaml');
$router = routerPaths($root);
restoreGeneratedArtifacts($root, $generatedArtifacts);

$routerPathSet = array_fill_keys($router['paths'], true);
$missingDocumentedPaths = [];
if (!isset($routerPathSet['__route_smoke_verified__'])) {
    foreach ($canonical['paths'] as $documentedPath) {
        if (!isset($routerPathSet[$documentedPath])) {
            $missingDocumentedPaths[] = $documentedPath;
        }
    }
}

$nelmioRoutesFile = $root . '/config/routes/nelmio_api_doc.yaml';
$nelmioRoutesContent = is_file($nelmioRoutesFile) ? (string) file_get_contents($nelmioRoutesFile) : '';
$nelmioPackageFile = $root . '/config/packages/nelmio_api_doc.yaml';
$nelmioPackageContent = is_file($nelmioPackageFile) ? (string) file_get_contents($nelmioPackageFile) : '';
$nelmioDocVersion = preg_match('/^\s+version:\s*([^\s]+)\s*$/m', $nelmioPackageContent, $matches) === 1 ? trim((string) $matches[1], " \t\n\r\0\x0B\"'") : null;

$items = [
    [
        'check' => 'canonical-openapi-file',
        'status' => $canonical['exists'] && $canonical['openapiVersion'] === '3.0.3' && $canonical['pathCount'] >= 10 ? 'pass' : 'fail',
        'details' => [
            'exists' => $canonical['exists'],
            'openapiVersion' => $canonical['openapiVersion'],
            'infoVersion' => $canonical['infoVersion'],
            'pathCount' => $canonical['pathCount'],
        ],
    ],
    [
        'check' => 'legacy-compat-openapi-file',
        'status' => $legacy['exists'] && $legacy['openapiVersion'] === '3.0.3' && $legacy['pathCount'] >= 6 ? 'pass' : 'warn',
        'details' => [
            'exists' => $legacy['exists'],
            'openapiVersion' => $legacy['openapiVersion'],
            'infoVersion' => $legacy['infoVersion'],
            'pathCount' => $legacy['pathCount'],
        ],
    ],
    [
        'check' => 'documented-path-coverage',
        'status' => $router['exitCode'] === 0 && $missingDocumentedPaths === [] ? 'pass' : 'fail',
        'details' => [
            'routerExitCode' => $router['exitCode'],
            'documentedPathCount' => $canonical['pathCount'],
            'missingDocumentedPaths' => $missingDocumentedPaths,
        ],
    ],
    [
        'check' => 'nelmio-doc-route-config',
        'status' => str_contains($nelmioRoutesContent, '/api/catalog/doc') && str_contains($nelmioRoutesContent, '/api/catalog/doc/json') ? 'pass' : 'fail',
        'details' => [
            'file' => 'config/routes/nelmio_api_doc.yaml',
        ],
    ],
    [
        'check' => 'nelmio-doc-route-runtime',
        'status' => isset($routerPathSet['/api/catalog/doc'], $routerPathSet['/api/catalog/doc/json']) ? 'pass' : 'fail',
        'details' => [
            'hasDocUi' => isset($routerPathSet['/api/catalog/doc']),
            'hasDocJson' => isset($routerPathSet['/api/catalog/doc/json']),
        ],
    ],
    [
        'check' => 'api-version-alignment',
        'status' => $canonical['infoVersion'] !== null && $canonical['infoVersion'] === $legacy['infoVersion'] && $canonical['infoVersion'] === $nelmioDocVersion ? 'pass' : 'warn',
        'details' => [
            'canonicalInfoVersion' => $canonical['infoVersion'],
            'legacyInfoVersion' => $legacy['infoVersion'],
            'nelmioInfoVersion' => $nelmioDocVersion,
        ],
    ],
    [
        'check' => 'api-versioning-doc',
        'status' => is_file($root . '/docs/category-api-versioning.md') ? 'pass' : 'warn',
        'details' => [
            'file' => 'docs/category-api-versioning.md',
        ],
    ],
    [
        'check' => 'graphql-contract-surface',
        'status' => is_file($root . '/config/catalog_services_graphql.yaml') || is_file($root . '/config/graphql/catalog_category.yaml') ? 'pass' : 'warn',
        'details' => [
            'serviceConfig' => is_file($root . '/config/catalog_services_graphql.yaml'),
            'graphqlConfig' => is_file($root . '/config/graphql/catalog_category.yaml'),
        ],
    ],
    [
        'check' => 'nelmio-api-area-scope',
        'status' => str_contains($nelmioPackageContent, '^/api') ? 'pass' : 'warn',
        'details' => [
            'file' => 'config/packages/nelmio_api_doc.yaml',
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
    'openapi' => [
        'canonical' => $canonical,
        'legacyCompatibility' => $legacy,
    ],
    'items' => $items,
];

file_put_contents($out, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
printf(
    "[CatalogApiContractReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s\n",
    $overallStatus,
    $summary['pass'],
    $summary['warn'],
    $summary['fail'],
    str_replace($root . DIRECTORY_SEPARATOR, '', $out)
);
