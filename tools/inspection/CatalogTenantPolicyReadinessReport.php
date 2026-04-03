<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-tenant-policy-readiness-report.json';

function text(string $path): string
{
    return is_file($path) ? (string) file_get_contents($path) : '';
}

function hasAll(string $path, string ...$needles): bool
{
    $content = text($path);
    foreach ($needles as $needle) {
        if (!str_contains($content, $needle)) {
            return false;
        }
    }

    return true;
}

$resolver = $root . '/src/Service/Security/ExternalIdentityContextResolver.php';
$resolverInterface = $root . '/src/ServiceInterface/Security/SecurityExternalIdentityContextResolverInterface.php';
$mutationAuthorization = $root . '/src/Service/CategoryMutationAuthorizationService.php';
$searchController = $root . '/src/Controller/CategorySearchController.php';
$mapper = $root . '/src/Service/Security/ExternalIdentityContextMapper.php';
$securityDoc = $root . '/docs/category-security-readiness.md';
$composer = $root . '/composer.json';
$doc = $root . '/docs/category-tenant-policy-readiness.md';

$items = [
    [
        'check' => 'external-identity-resolver-present',
        'status' => is_file($resolver) && is_file($resolverInterface) ? 'pass' : 'fail',
        'details' => ['resolver' => 'src/Service/Security/ExternalIdentityContextResolver.php'],
    ],
    [
        'check' => 'resolver-validates-bearer-jwt',
        'status' => hasAll($resolver, 'Authorization', 'Bearer', 'validate(', 'map(') ? 'pass' : 'warn',
        'details' => ['file' => 'src/Service/Security/ExternalIdentityContextResolver.php'],
    ],
    [
        'check' => 'mapper-supports-tenant-and-category-roles',
        'status' => hasAll($mapper, 'tenant', 'category_roles', 'catalog_roles') ? 'pass' : 'warn',
        'details' => ['file' => 'src/Service/Security/ExternalIdentityContextMapper.php'],
    ],
    [
        'check' => 'mutation-authorization-enforces-tenant-boundary',
        'status' => hasAll($mutationAuthorization, 'Cross-tenant category mutation is not allowed', 'categoryTenant(', 'externalTenantRoleAllows(') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CategoryMutationAuthorizationService.php'],
    ],
    [
        'check' => 'search-controller-enforces-tenant-scope',
        'status' => hasAll($searchController, 'Cross-tenant category search is not allowed', 'applyTenantScope(', "published'] ??= true") ? 'pass' : 'fail',
        'details' => ['file' => 'src/Controller/CategorySearchController.php'],
    ],
    [
        'check' => 'tenant-policy-doc-present',
        'status' => is_file($doc) ? 'pass' : 'warn',
        'details' => ['file' => 'docs/category-tenant-policy-readiness.md'],
    ],
    [
        'check' => 'security-doc-retained',
        'status' => is_file($securityDoc) ? 'pass' : 'warn',
        'details' => ['file' => 'docs/category-security-readiness.md'],
    ],
    [
        'check' => 'composer-report-wiring',
        'status' => str_contains(text($composer), 'report:tenant-policy-readiness') ? 'pass' : 'warn',
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
echo sprintf('[CatalogTenantPolicyReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s'.PHP_EOL, $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
