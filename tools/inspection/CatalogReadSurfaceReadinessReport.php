<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-read-surface-readiness-report.json';

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

$treeController = $root . '/src/Controller/CategoryApiController.php';
$storefrontController = $root . '/src/Controller/CategoryStorefrontController.php';
$adminController = $root . '/src/Controller/Admin/CategoryAdminController.php';
$adminApiController = $root . '/src/Controller/Api/CategoryAdminApiController.php';
$merchantController = $root . '/src/Controller/Merchant/CategoryMerchantController.php';
$readService = $root . '/src/Service/CategoryProjectionReadService.php';
$scopeService = $root . '/src/Service/CategoryReadScopeService.php';
$docs = $root . '/docs/category-read-surface-readiness.md';
$composer = $root . '/composer.json';
$apiCanonical = $root . '/api/catalog-openapi.yaml';
$apiLegacy = $root . '/api/category-openapi.yaml';
$tests = $root . '/tests/Service/CategoryReadScopeServiceTest.php';

$items = [
    [
        'check' => 'projection-read-service-present',
        'status' => containsAll($readService, 'class CategoryProjectionReadService', 'category_projection', 'fetchAllAssociative') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CategoryProjectionReadService.php'],
    ],
    [
        'check' => 'read-scope-service-present',
        'status' => containsAll($scopeService, 'class CategoryReadScopeService', 'Cross-tenant category read is not allowed') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CategoryReadScopeService.php'],
    ],
    [
        'check' => 'tree-api-uses-read-services',
        'status' => containsAll($treeController, 'categoryProjectionReadService->tree', 'categoryReadScopeService->applyTenantScope') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Controller/CategoryApiController.php'],
    ],
    [
        'check' => 'storefront-api-uses-read-services',
        'status' => containsAll($storefrontController, 'categoryProjectionReadService->list', 'categoryReadScopeService->applyTenantScope') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Controller/CategoryStorefrontController.php'],
    ],
    [
        'check' => 'admin-ui-uses-projection-data',
        'status' => containsAll($adminController, 'categoryProjectionReadService->list', 'categoryProjectionReadService->tree', 'categoryProjectionReadService->findOne') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Controller/Admin/CategoryAdminController.php'],
    ],
    [
        'check' => 'admin-api-uses-projection-data',
        'status' => containsAll($adminApiController, 'categoryProjectionReadService->list') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Controller/Api/CategoryAdminApiController.php'],
    ],
    [
        'check' => 'merchant-view-uses-external-tenant-context',
        'status' => containsAll($merchantController, "IsGranted('IS_AUTHENTICATED_FULLY')", 'externalIdentityContextResolver->resolveFromRequest', 'categoryProjectionReadService->list') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Controller/Merchant/CategoryMerchantController.php'],
    ],
    [
        'check' => 'demo-read-arrays-removed-from-active-controllers',
        'status' => !containsAll($treeController, "'Root'", "'Electronics'") && !containsAll($storefrontController, "'Root'", "'Electronics'") && !containsAll($adminController, "'Root'", "'Electronics'") && !containsAll($adminApiController, "'Root'", "'Electronics'") ? 'pass' : 'fail',
        'details' => ['files' => ['src/Controller/CategoryApiController.php', 'src/Controller/CategoryStorefrontController.php', 'src/Controller/Admin/CategoryAdminController.php', 'src/Controller/Api/CategoryAdminApiController.php']],
    ],
    [
        'check' => 'canonical-openapi-read-routes',
        'status' => containsAll($apiCanonical, '/api/category/tree:', '/api/category/storefront:') ? 'pass' : 'warn',
        'details' => ['file' => 'api/catalog-openapi.yaml'],
    ],
    [
        'check' => 'legacy-openapi-read-routes',
        'status' => containsAll($apiLegacy, '/api/category/tree:', '/api/category/storefront:') ? 'pass' : 'warn',
        'details' => ['file' => 'api/category-openapi.yaml'],
    ],
    [
        'check' => 'read-surface-doc-present',
        'status' => is_file($docs) ? 'pass' : 'warn',
        'details' => ['file' => 'docs/category-read-surface-readiness.md'],
    ],
    [
        'check' => 'read-surface-tests-present',
        'status' => is_file($tests) ? 'pass' : 'warn',
        'details' => ['file' => 'tests/Service/CategoryReadScopeServiceTest.php'],
    ],
    [
        'check' => 'composer-report-wiring',
        'status' => str_contains(fileText($composer), 'report:read-surface-readiness') ? 'pass' : 'warn',
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
echo sprintf('[CatalogReadSurfaceReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s'.PHP_EOL, $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
