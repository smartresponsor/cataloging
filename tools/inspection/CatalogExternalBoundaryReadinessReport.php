<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-external-boundary-readiness-report.json';

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

$attachmentRequest = $root . '/src/Request/CategoryAttachmentAddRequest.php';
$attachmentService = $root . '/src/Service/AttachmentService.php';
$attachmentRepository = $root . '/src/Repository/CatalogAttachmentRepository.php';
$attachmentGateway = $root . '/src/Attachment/NullAttachmentReferenceGateway.php';
$attachmentGatewayInterface = $root . '/src/AttachmentInterface/AttachmentReferenceGatewayInterface.php';
$externalIdentityMapper = $root . '/src/Service/Security/ExternalIdentityContextMapper.php';
$externalIdentityMapperInterface = $root . '/src/ServiceInterface/Security/SecurityExternalIdentityContextMapperInterface.php';
$externalIdentityContext = $root . '/src/Security/ExternalIdentityContext.php';
$securityDoc = $root . '/docs/security/jwks.md';
$boundaryDoc = $root . '/docs/category-external-boundary-readiness.md';
$composer = $root . '/composer.json';
$migration = $root . '/migrations/Version20251102084000_category_attachment_external_boundary.php';

$items = [
    [
        'check' => 'attachment-request-requires-external-reference',
        'status' => containsAll($attachmentRequest, 'provider is required', 'external_attachment_id is required') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Request/CategoryAttachmentAddRequest.php'],
    ],
    [
        'check' => 'attachment-service-uses-reference-gateway',
        'status' => containsAll($attachmentService, 'AttachmentReferenceGatewayInterface', 'assertBindable', 'externalAttachmentId') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/AttachmentService.php'],
    ],
    [
        'check' => 'attachment-repository-stores-provider-and-external-id',
        'status' => containsAll($attachmentRepository, 'provider', 'external_attachment_id', 'ux_category_attachment_external_binding') ? 'pass' : 'fail',
        'details' => ['file' => 'src/Repository/CatalogAttachmentRepository.php'],
    ],
    [
        'check' => 'attachment-boundary-has-no-file-upload-operations',
        'status' => !str_contains(fileText($attachmentService), 'move_uploaded_file') && !str_contains(fileText($attachmentRepository), 'fopen(') ? 'pass' : 'fail',
        'details' => ['files' => ['src/Service/AttachmentService.php', 'src/Repository/CatalogAttachmentRepository.php']],
    ],
    [
        'check' => 'attachment-gateway-interface-present',
        'status' => is_file($attachmentGatewayInterface) && is_file($attachmentGateway) ? 'pass' : 'warn',
        'details' => ['interface' => 'src/AttachmentInterface/AttachmentReferenceGatewayInterface.php'],
    ],
    [
        'check' => 'attachment-boundary-migration-present',
        'status' => is_file($migration) ? 'pass' : 'warn',
        'details' => ['file' => 'migrations/Version20251102084000_category_attachment_external_boundary.php'],
    ],
    [
        'check' => 'external-identity-context-mapper-present',
        'status' => is_file($externalIdentityMapper) && is_file($externalIdentityMapperInterface) && is_file($externalIdentityContext) ? 'pass' : 'fail',
        'details' => ['file' => 'src/Service/Security/ExternalIdentityContextMapper.php'],
    ],
    [
        'check' => 'external-identity-context-maps-sub-tenant-roles',
        'status' => containsAll($externalIdentityMapper, 'sub', 'tenant', 'roles', 'category_roles') ? 'pass' : 'warn',
        'details' => ['file' => 'src/Service/Security/ExternalIdentityContextMapper.php'],
    ],
    [
        'check' => 'oidc-boundary-doc-present',
        'status' => is_file($securityDoc) ? 'pass' : 'warn',
        'details' => ['file' => 'docs/security/jwks.md'],
    ],
    [
        'check' => 'external-boundary-doc-present',
        'status' => is_file($boundaryDoc) ? 'pass' : 'warn',
        'details' => ['file' => 'docs/category-external-boundary-readiness.md'],
    ],
    [
        'check' => 'composer-report-wiring',
        'status' => str_contains(fileText($composer), 'report:external-boundary-readiness') ? 'pass' : 'warn',
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
echo sprintf('[CatalogExternalBoundaryReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s'.PHP_EOL, $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
