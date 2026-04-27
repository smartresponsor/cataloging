<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-graphql-store-readiness-report.json';

function fileText(string $path): string
{
    return is_file($path) ? (string) file_get_contents($path) : '';
}

function containsAll(string $path, string ...$needles): bool
{
    $text = fileText($path);
    foreach ($needles as $needle) {
        if (!str_contains($text, $needle)) {
            return false;
        }
    }

    return true;
}

$storeController = $root . '/src/Controller/CategoryStoreApiController.php';
$optimizer = $root . '/src/Service/ReadOptimizer.php';
$graphqlResolver = $root . '/src/Service/GraphqlResolver.php';
$graphqlFacetResolver = $root . '/src/Service/GraphqlFacetResolver.php';
$docs = $root . '/docs/category-graphql-store-readiness.md';
$composer = $root . '/composer.json';
$tests = [
    $root . '/tests/Service/ReadOptimizerTest.php',
    $root . '/tests/Service/GraphqlResolverTest.php',
];

$items = [
    [
        'check' => 'store-api-uses-read-optimizer',
        'status' => containsAll($storeController, 'optimizer->getTree', "'published' => true") ? 'pass' : 'fail',
        'details' => ['file' => 'src/Controller/CategoryStoreApiController.php'],
    ],
    [
        'check' => 'read-optimizer-uses-projection-read-service',
        'status' => containsAll($optimizer, 'CategoryProjectionReadServiceInterface', 'categoryProjectionReadService->tree') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/ReadOptimizer.php'],
    ],
    [
        'check' => 'read-optimizer-no-file-metrics',
        'status' => !str_contains(fileText($optimizer), 'file_put_contents') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/ReadOptimizer.php'],
    ],
    [
        'check' => 'graphql-category-read-uses-projection',
        'status' => containsAll($graphqlResolver, 'categoryProjectionReadService->findOne', 'category_projection') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/GraphqlResolver.php'],
    ],
    [
        'check' => 'graphql-path-read-uses-projection',
        'status' => containsAll($graphqlResolver, 'categoryPath', 'FROM category_projection WHERE path IN') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/GraphqlResolver.php'],
    ],
    [
        'check' => 'graphql-facet-read-uses-search-service',
        'status' => containsAll($graphqlFacetResolver, 'CatalogSearchService', 'searchService->search') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/GraphqlFacetResolver.php'],
    ],
    [
        'check' => 'graphql-read-no-sqlite-memory-fallback',
        'status' => !str_contains(fileText($graphqlFacetResolver), 'sqlite::memory:') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/GraphqlFacetResolver.php'],
    ],
    [
        'check' => 'store-read-no-root-electronics-demo-array',
        'status' => !str_contains(fileText($optimizer), "'Root'") && !str_contains(fileText($optimizer), "'Electronics'") ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/ReadOptimizer.php'],
    ],
    [
        'check' => 'docs-present',
        'status' => is_file($docs) ? 'pass' : 'warn',
        'details' => ['file' => 'docs/category-graphql-store-readiness.md'],
    ],
    [
        'check' => 'tests-present',
        'status' => array_reduce($tests, static fn (bool $carry, string $file): bool => $carry && is_file($file), true) ? 'pass' : 'warn',
        'details' => ['files' => ['tests/Service/ReadOptimizerTest.php', 'tests/Service/GraphqlResolverTest.php']],
    ],
    [
        'check' => 'composer-report-wiring',
        'status' => str_contains(fileText($composer), 'report:graphql-store-readiness') ? 'pass' : 'warn',
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
echo sprintf('[CatalogGraphqlStoreReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s'.PHP_EOL, $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
