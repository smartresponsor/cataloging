<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-security-readiness-report.json';

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
 * @param list<string> $files
 *
 * @return list<array{file:string,pattern:string,roles:list<string>}>
 */
function accessControlRules(array $files): array
{
    $rules = [];
    foreach ($files as $file) {
        if (!is_file($file)) {
            continue;
        }
        $content = (string) file_get_contents($file);
        if (preg_match_all('/-\s*\{\s*path:\s*([^,}]+),\s*roles:\s*([^}]+)\}/', $content, $matches, PREG_SET_ORDER) !== false) {
            foreach ($matches as $match) {
                $pattern = trim((string) $match[1], " \t\n\r\0\x0B\"'");
                $rolesRaw = trim((string) $match[2], " \t\n\r\0\x0B\"'");
                $roles = array_values(array_filter(array_map(
                    static fn (string $role): string => trim($role, " []\t\n\r\0\x0B\"'"),
                    preg_split('/\s*,\s*/', $rolesRaw) ?: []
                )));
                $rules[] = ['file' => str_replace($GLOBALS['root'] . '/', '', $file), 'pattern' => $pattern, 'roles' => $roles];
            }
        }
    }

    return $rules;
}

/**
 * @return array{exitCode:int,paths:list<string>}
 */
function routerPaths(string $root): array
{
    $result = commandResult('cd ' . escapeshellarg($root) . ' && APP_ENV=prod APP_DEBUG=0 php bin/console debug:router --format=json --no-ansi');
    if ($result['exitCode'] !== 0) {
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

/**
 * @param list<array{file:string,pattern:string,roles:list<string>}> $rules
 */
function pathProtectedByAccessControl(string $path, array $rules): bool
{
    foreach ($rules as $rule) {
        $roles = $rule['roles'];
        if ($roles === [] || array_intersect($roles, ['IS_AUTHENTICATED_ANONYMOUSLY', 'PUBLIC_ACCESS']) !== []) {
            continue;
        }
        if (@preg_match('#' . $rule['pattern'] . '#', $path) === 1) {
            return true;
        }
    }

    return false;
}

function fileContains(string $path, string $needle): bool
{
    return is_file($path) && str_contains((string) file_get_contents($path), $needle);
}

function policyAuthorizedMutationRoute(string $controllerSource, string $serviceNeedle, string $voterNeedle): bool
{
    return fileContains($controllerSource, $serviceNeedle) || fileContains($controllerSource, $voterNeedle);
}

$generatedArtifacts = ['config/reference.php'];
$router = routerPaths($root);
restoreGeneratedArtifacts($root, $generatedArtifacts);
$routeSet = array_fill_keys($router['paths'], true);
$rules = accessControlRules([
    $root . '/config/packages/catalog_category_security_api.yaml',
    $root . '/config/packages/catalog_security_access.yaml',
    $root . '/config/packages/catalog_category_security.yaml',
    $root . '/config/packages/catalog_security.yaml',
]);

$providerSource = $root . '/src/Security/JwtUserProvider.php';
$servicesSource = $root . '/config/services.yaml';
$securityApiSource = $root . '/config/packages/catalog_category_security_api.yaml';
$accessAssignmentRepository = $root . '/src/Repository/CategoryAccessAssignmentRepository.php';
$mutationAuthorizationService = $root . '/src/Service/CategoryMutationAuthorizationService.php';
$adminController = $root . '/src/Controller/Admin/CategoryAdminController.php';
$adminApiController = $root . '/src/Controller/Api/CategoryAdminApiController.php';
$categoryApiController = $root . '/src/Controller/CategoryApiController.php';
$attachmentController = $root . '/src/Controller/CategoryAttachmentController.php';
$webhookController = $root . '/src/Controller/WebhookController.php';
$jwksDoc = $root . '/docs/security/jwks.md';
$oidcVerifier = $root . '/src/Service/OidcJwtVerifier.php';
$oidcValidator = $root . '/src/Service/OidcJwtValidator.php';

$items = [
    [
        'check' => 'jwt-provider-least-privilege-default',
        'status' => fileContains($providerSource, 'private readonly array $defaultRoles = [\'ROLE_USER\']') && !fileContains($providerSource, 'private readonly array $defaultRoles = [\'ROLE_ADMIN\']') ? 'pass' : 'fail',
        'details' => [
            'file' => 'src/Security/JwtUserProvider.php',
        ],
    ],
    [
        'check' => 'jwt-admin-allowlist-wiring',
        'status' => fileContains($servicesSource, 'CATEGORY_API_ADMIN_IDENTIFIERS') ? 'pass' : 'warn',
        'details' => [
            'file' => 'config/services.yaml',
        ],
    ],
    [
        'check' => 'api-firewall-configured',
        'status' => fileContains($securityApiSource, 'category_api:') && fileContains($securityApiSource, 'provider: category_jwt') ? 'pass' : 'fail',
        'details' => [
            'file' => 'config/packages/catalog_category_security_api.yaml',
        ],
    ],
    [
        'check' => 'admin-ui-route-protected',
        'status' => fileContains($adminController, "#[IsGranted('ROLE_ADMIN')]") || pathProtectedByAccessControl('/admin/category', $rules) ? 'pass' : 'fail',
        'details' => [
            'routePrefix' => '/admin/category',
        ],
    ],
    [
        'check' => 'admin-api-route-protected',
        'status' => fileContains($adminApiController, "#[IsGranted('ROLE_ADMIN')]") || pathProtectedByAccessControl('/api/admin/category/list', $rules) ? 'pass' : 'fail',
        'details' => [
            'routePrefix' => '/api/admin/category',
        ],
    ],
    [
        'check' => 'publish-route-protected',
        'status' => fileContains($categoryApiController, "#[IsGranted('ROLE_ADMIN')]") || pathProtectedByAccessControl('/api/category/{id}/publish', $rules) ? 'pass' : 'fail',
        'details' => [
            'route' => '/api/category/{id}/publish',
            'presentInRouter' => isset($routeSet['/api/category/{id}/publish']),
        ],
    ],
    [
        'check' => 'move-route-protected',
        'status' => fileContains($categoryApiController, "#[IsGranted('ROLE_ADMIN')]") || pathProtectedByAccessControl('/api/category/{id}/move', $rules) ? 'pass' : 'fail',
        'details' => [
            'route' => '/api/category/{id}/move',
            'presentInRouter' => isset($routeSet['/api/category/{id}/move']),
        ],
    ],
    [
        'check' => 'attachment-write-protected',
        'status' => (pathProtectedByAccessControl('/api/category/attachment', $rules)
            && fileContains($attachmentController, 'authorizationService->assertCanAttach')
            && fileContains($attachmentController, 'authorizationService->assertCanDetach')) ? 'pass' : 'warn',
        'details' => [
            'routes' => ['/api/category/attachment', '/api/category/attachment/{attachmentId}'],
        ],
    ],
    [
        'check' => 'access-assignment-repository-durable',
        'status' => fileContains($accessAssignmentRepository, 'private readonly ?Connection $connection') && fileContains($servicesSource, "App\\RepositoryInterface\\CategoryAccessAssignmentRepositoryInterface") ? 'pass' : 'warn',
        'details' => [
            'file' => 'src/Repository/CategoryAccessAssignmentRepository.php',
        ],
    ],
    [
        'check' => 'tenant-aware-mutation-policy',
        'status' => fileContains($mutationAuthorizationService, 'Cross-tenant category mutation is not allowed') ? 'pass' : 'warn',
        'details' => [
            'service' => 'src/Service/CategoryMutationAuthorizationService.php',
        ],
    ],
    [
        'check' => 'mutation-routes-policy-authorized',
        'status' => policyAuthorizedMutationRoute($categoryApiController, 'categoryMutationAuthorizationService->assertCanMove', 'CategoryVoter::EDIT')
            && policyAuthorizedMutationRoute($categoryApiController, 'categoryMutationAuthorizationService->assertCanPublish', 'CategoryVoter::PUBLISH') ? 'pass' : 'warn',
        'details' => [
            'controller' => 'src/Controller/CategoryApiController.php',
        ],
    ],

    [
        'check' => 'webhook-test-route-protected',
        'status' => fileContains($webhookController, "#[IsGranted('ROLE_ADMIN')]") || pathProtectedByAccessControl('/api/category/webhook/test', $rules) ? 'pass' : 'fail',
        'details' => [
            'route' => '/api/category/webhook/test',
            'presentInRouter' => isset($routeSet['/api/category/webhook/test']),
        ],
    ],
    [
        'check' => 'oidc-jwks-readiness-doc',
        'status' => is_file($jwksDoc) && is_file($oidcVerifier) && is_file($oidcValidator) ? 'pass' : 'warn',
        'details' => [
            'doc' => 'docs/security/jwks.md',
            'verifierClass' => is_file($oidcVerifier),
            'validatorClass' => is_file($oidcValidator),
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
    'accessControlRules' => $rules,
    'items' => $items,
];

file_put_contents($out, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
printf(
    "[CatalogSecurityReadinessReport] status=%s pass=%d warn=%d fail=%d written to %s\n",
    $overallStatus,
    $summary['pass'],
    $summary['warn'],
    $summary['fail'],
    str_replace($root . DIRECTORY_SEPARATOR, '', $out)
);
