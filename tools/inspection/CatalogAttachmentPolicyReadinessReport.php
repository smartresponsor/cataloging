<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-attachment-policy-readiness-report.json';

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

$controller = $root . '/src/Controller/CategoryAttachmentController.php';
$authorizationService = $root . '/src/Service/CategoryAttachmentAuthorizationService.php';
$repository = $root . '/src/Repository/CatalogAttachmentRepository.php';
$repositoryInterface = $root . '/src/RepositoryInterface/CatalogAttachmentRepositoryInterface.php';
$securityConfig = $root . '/config/packages/catalog_category_security_api.yaml';
$doc = $root . '/docs/category-attachment-policy-readiness.md';
$composer = $root . '/composer.json';
$tests = $root . '/tests/Category/CategoryAttachmentAuthorizationServiceTest.php';

$items = [
    [
        'check' => 'attachment-authorization-service-present',
        'status' => is_file($authorizationService) ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CategoryAttachmentAuthorizationService.php'],
    ],
    [
        'check' => 'attachment-list-requires-authorization-service',
        'status' => containsAll($controller, 'assertCanList(', 'CategoryAttachmentAuthorizationService') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Controller/CategoryAttachmentController.php'],
    ],
    [
        'check' => 'attachment-add-requires-policy-check',
        'status' => containsAll($controller, 'assertCanAttach(') && containsAll($authorizationService, 'Category attachment binding is not allowed') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Controller/CategoryAttachmentController.php'],
    ],
    [
        'check' => 'attachment-delete-requires-policy-check',
        'status' => containsAll($controller, 'assertCanDetach(') && containsAll($authorizationService, 'Category attachment deletion is not allowed') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Controller/CategoryAttachmentController.php'],
    ],
    [
        'check' => 'attachment-policy-enforces-tenant-boundary',
        'status' => containsAll($authorizationService, 'Cross-tenant category attachment operation is not allowed', 'External tenant identity is required') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/CategoryAttachmentAuthorizationService.php'],
    ],
    [
        'check' => 'attachment-policy-uses-category-voter-and-tenant-roles',
        'status' => containsAll($authorizationService, 'CategoryVoter::EDIT', 'CategoryVoter::VIEW', 'tenantRolePolicy->allow') ? 'pass' : 'warn',
        'details' => ['file' => 'src/Service/CategoryAttachmentAuthorizationService.php'],
    ],
    [
        'check' => 'attachment-repository-can-resolve-single-binding',
        'status' => containsAll($repository, 'public function findOne(', 'attachment_id = :attachment_id') && str_contains(fileText($repositoryInterface), 'public function findOne(') ? 'pass' : 'fail',
        'details' => ['files' => ['src/Repository/CatalogAttachmentRepository.php', 'src/RepositoryInterface/CatalogAttachmentRepositoryInterface.php']],
    ],
    [
        'check' => 'attachment-security-access-control-requires-authentication',
        'status' => str_contains(fileText($securityConfig), '^/api/category/attachment') && str_contains(fileText($securityConfig), 'IS_AUTHENTICATED_FULLY') ? 'pass' : 'warn',
        'details' => ['file' => 'config/packages/catalog_category_security_api.yaml'],
    ],
    [
        'check' => 'attachment-policy-doc-present',
        'status' => is_file($doc) ? 'pass' : 'warn',
        'details' => ['file' => 'docs/category-attachment-policy-readiness.md'],
    ],
    [
        'check' => 'attachment-policy-tests-present',
        'status' => is_file($tests) ? 'pass' : 'warn',
        'details' => ['file' => 'tests/Category/CategoryAttachmentAuthorizationServiceTest.php'],
    ],
    [
        'check' => 'composer-report-wiring',
        'status' => str_contains(fileText($composer), 'report:attachment-policy-readiness') ? 'pass' : 'warn',
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
echo sprintf('[CatalogAttachmentPolicyReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s'.PHP_EOL, $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
