<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-search-readiness-report.json';

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

$searchService = $root . '/src/Service/CatalogSearchService.php';
$searchController = $root . '/src/Controller/CategorySearchController.php';
$apiCanonical = $root . '/api/catalog-openapi.yaml';
$apiLegacy = $root . '/api/category-openapi.yaml';
$sqlSchema = $root . '/config/sql/catalog_mysql_infra_category.sql';
$migration = $root . '/migrations/Version20251102081000_category_projection_search_runtime_hardening.php';
$docs = $root . '/docs/category-search-readiness.md';
$composer = $root . '/composer.json';

$items = [
    [
        'check' => 'projection-backed-search-service',
        'status' => containsAll($searchService, 'category_projection', 'ManagerRegistry', 'fetchAllAssociative') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CatalogSearchService.php'],
    ],
    [
        'check' => 'no-in-memory-dataset',
        'status' => !str_contains(fileText($searchService), 'private array $data') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CatalogSearchService.php'],
    ],
    [
        'check' => 'no-file-based-search-side-effects',
        'status' => !str_contains(fileText($searchService), 'file_put_contents') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CatalogSearchService.php'],
    ],
    [
        'check' => 'controller-filter-support',
        'status' => containsAll($searchController, 'tenant', 'locale', 'workflow_state', 'published', 'limit', 'offset', 'order', 'direction') ? 'pass' : 'warn',
        'details' => ['file' => 'src/Controller/CategorySearchController.php'],
    ],
    [
        'check' => 'canonical-openapi-search-path',
        'status' => containsAll($apiCanonical, '/api/category/search:', 'workflow_state', 'published', 'direction') ? 'pass' : 'fail',
        'details' => ['file' => 'api/catalog-openapi.yaml'],
    ],
    [
        'check' => 'legacy-openapi-search-path',
        'status' => containsAll($apiLegacy, '/api/category/search:', 'workflow_state', 'published', 'direction') ? 'pass' : 'warn',
        'details' => ['file' => 'api/category-openapi.yaml'],
    ],
    [
        'check' => 'projection-search-indexes-sql',
        'status' => containsAll($sqlSchema, 'idx_category_projection_name', 'idx_category_projection_tenant_locale', 'idx_category_projection_workflow_state', 'idx_category_projection_updated_at') ? 'pass' : 'fail',
        'details' => ['file' => 'config/sql/catalog_mysql_infra_category.sql'],
    ],
    [
        'check' => 'projection-search-indexes-migration',
        'status' => containsAll($migration, 'idx_category_projection_name', 'idx_category_projection_tenant_locale', 'idx_category_projection_workflow_state', 'idx_category_projection_updated_at') ? 'pass' : 'fail',
        'details' => ['file' => 'migrations/Version20251102081000_category_projection_search_runtime_hardening.php'],
    ],
    [
        'check' => 'search-readiness-doc',
        'status' => is_file($docs) ? 'pass' : 'warn',
        'details' => ['file' => 'docs/category-search-readiness.md'],
    ],
    [
        'check' => 'composer-report-wiring',
        'status' => str_contains(fileText($composer), 'report:search-readiness') ? 'pass' : 'warn',
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
echo sprintf('[CatalogSearchReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s'.PHP_EOL, $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
