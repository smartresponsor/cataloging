<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-boundary-readiness-report.json';

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

$boundaryDoc = $root . '/docs/category-boundary-policy.md';
$graphqlDoc = $root . '/docs/category-graphql-boundary.md';
$deliveryDoc = $root . '/docs/category-delivery-surface-boundary.md';
$proofDoc = $root . '/docs/category-operational-proof-boundary.md';
$antoraBoundaryPage = $root . '/docs/modules/ROOT/pages/boundaries.adoc';
$antoraNav = $root . '/docs/modules/ROOT/nav.adoc';
$graphqlResolver = $root . '/src/Service/CatalogGraphqlResolverService.php';
$graphqlFacetResolver = $root . '/src/Service/CatalogGraphqlFacetResolverService.php';
$storeController = $root . '/src/Controller/Catalog/CatalogCategoryStoreApiController.php';
$storefrontController = $root . '/src/Controller/Catalog/CatalogCategoryApiController.php';
$searchController = $root . '/src/Controller/Catalog/CatalogCategorySearchController.php';
$adminController = $root . '/src/Controller/Catalog/CatalogCategoryAdminController.php';
$adminApiController = $root . '/src/Controller/Catalog/CatalogCategoryAdminApiController.php';
$merchantController = $root . '/src/Controller/Catalog/CatalogCategoryMerchantController.php';
$readScopeService = $root . '/src/Service/CatalogCategoryReadScopeService.php';
$projectionReadService = $root . '/src/Service/CatalogCategoryProjectionReadService.php';
$rcDoc = $root . '/docs/category-rc-readiness.md';
$composer = $root . '/composer.json';

$srcDir = $root . '/src';
$reportInspectionReferences = [];
if (is_dir($srcDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($srcDir));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo) {
            continue;
        }
        if (!$file->isFile() || 'php' !== $file->getExtension()) {
            continue;
        }
        $path = $file->getPathname();
        $text = fileText($path);
        if (str_contains($text, 'report/inspection/')) {
            $reportInspectionReferences[] = str_replace($root . '/', '', $path);
        }
    }
}

$items = [
    [
        'check' => 'boundary-policy-doc-present',
        'status' => is_file($boundaryDoc) ? 'pass' : 'fail',
        'details' => ['file' => 'docs/category-boundary-policy.md'],
    ],
    [
        'check' => 'graphql-boundary-doc-classifies-secondary-adapter',
        'status' => containsAll($graphqlDoc, 'secondary read adapter', 'compatibility surface', 'not the primary domain boundary') ? 'pass' : 'fail',
        'details' => ['file' => 'docs/category-graphql-boundary.md'],
    ],
    [
        'check' => 'delivery-surface-doc-enforces-adapter-shape',
        'status' => containsAll($deliveryDoc, 'delivery surfaces must remain adapters', 'write surfaces delegate', 'read surfaces delegate') ? 'pass' : 'fail',
        'details' => ['file' => 'docs/category-delivery-surface-boundary.md'],
    ],
    [
        'check' => 'operational-proof-boundary-doc-enforces-producer-only',
        'status' => containsAll($proofDoc, 'producer', 'tools/inspection/', 'must not drift into a self-hosted governance platform') ? 'pass' : 'fail',
        'details' => ['file' => 'docs/category-operational-proof-boundary.md'],
    ],
    [
        'check' => 'antora-boundary-entry-surface-present',
        'status' => containsAll($antoraBoundaryPage, 'Boundary policy', 'category-graphql-boundary.md', 'category-operational-proof-boundary.md') && str_contains(fileText($antoraNav), 'xref:boundaries.adoc[Boundary policy]') ? 'pass' : 'warn',
        'details' => ['file' => 'docs/modules/ROOT/pages/boundaries.adoc'],
    ],
    [
        'check' => 'graphql-resolvers-declared-as-secondary-adapters',
        'status' => containsAll($graphqlResolver, 'Secondary GraphQL read adapter', 'compatibility/convenience read surface') && containsAll($graphqlFacetResolver, 'Secondary GraphQL facet adapter', 'canonical search/read services') ? 'pass' : 'fail',
        'details' => ['files' => ['src/Service/CatalogGraphqlResolverService.php', 'src/Service/CatalogGraphqlFacetResolverService.php']],
    ],
    [
        'check' => 'delivery-controllers-delegate-to-shared-services',
        'status' => containsAll($storeController, 'CatalogReadOptimizerService')
            && containsAll($storefrontController, 'CategoryProjectionReadServiceInterface', 'CategoryReadScopeServiceInterface')
            && containsAll($searchController, 'CategoryReadScopeServiceInterface', 'CatalogSearchService')
            && containsAll($adminController, 'CategoryProjectionReadServiceInterface')
            && containsAll($adminApiController, 'CategoryProjectionReadServiceInterface')
            && containsAll($merchantController, 'CategoryProjectionReadServiceInterface', 'SecurityExternalIdentityContextResolverInterface') ? 'pass' : 'fail',
        'details' => ['controllers' => [
            'src/Controller/Catalog/CatalogCategoryStoreApiController.php',
            'src/Controller/Catalog/CatalogCategoryApiController.php',
            'src/Controller/Catalog/CatalogCategorySearchController.php',
            'src/Controller/Catalog/CatalogCategoryAdminController.php',
            'src/Controller/Catalog/CatalogCategoryAdminApiController.php',
            'src/Controller/Catalog/CatalogCategoryMerchantController.php',
        ]],
    ],
    [
        'check' => 'shared-read-services-centralize-delivery-seams',
        'status' => containsAll($readScopeService, 'applyTenantScope') && containsAll($projectionReadService, 'list(', 'tree(', 'findOne(') ? 'pass' : 'fail',
        'details' => ['files' => ['src/Service/CatalogCategoryReadScopeService.php', 'src/Service/CatalogCategoryProjectionReadService.php']],
    ],
    [
        'check' => 'runtime-src-does-not-depend-on-inspection-artifacts',
        'status' => [] === $reportInspectionReferences ? 'pass' : 'fail',
        'details' => ['reportInspectionReferences' => $reportInspectionReferences],
    ],
    [
        'check' => 'rc-doc-references-boundary-policy',
        'status' => str_contains(fileText($rcDoc), 'boundary readiness') ? 'pass' : 'warn',
        'details' => ['file' => 'docs/category-rc-readiness.md'],
    ],
    [
        'check' => 'composer-report-wiring',
        'status' => str_contains(fileText($composer), 'report:boundary-readiness') ? 'pass' : 'warn',
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
echo sprintf('[CatalogBoundaryReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s'.PHP_EOL, $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
