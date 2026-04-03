<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-dependency-baseline-report.json';

require_once $root . '/vendor/autoload.php';

/**
 * @return array<string, mixed>
 */
function hashFileIfPresent(string $root, string $relativePath): array
{
    $path = $root . '/' . $relativePath;

    if (!is_file($path)) {
        return [
            'path' => $relativePath,
            'exists' => false,
        ];
    }

    return [
        'path' => $relativePath,
        'exists' => true,
        'sha256' => hash_file('sha256', $path),
        'size' => filesize($path),
    ];
}

/**
 * @return list<array{path: string, status: string}>
 */
function collectGitStatus(string $root, string $scope): array
{
    $command = sprintf('git -C %s status --short -- %s 2>/dev/null', escapeshellarg($root), escapeshellarg($scope));
    $lines = array_values(array_filter(array_map('trim', explode("\n", (string) shell_exec($command)))));

    $rows = [];
    foreach ($lines as $line) {
        $status = trim(substr($line, 0, 2));
        $path = trim(substr($line, 2));
        if (str_starts_with($path, '->')) {
            $path = trim(substr($path, 2));
        }

        $rows[] = [
            'path' => $path,
            'status' => $status,
        ];
    }

    return $rows;
}

/**
 * @return array<string, string>
 */
function collectInstalledVersions(string $root, array $packages): array
{
    $installedFile = $root . '/vendor/composer/installed.php';
    if (!is_file($installedFile)) {
        return [];
    }

    $installed = require $installedFile;
    $versions = [];

    foreach (($installed['versions'] ?? []) as $name => $meta) {
        if (in_array($name, $packages, true)) {
            $versions[$name] = (string) ($meta['pretty_version'] ?? $meta['version'] ?? 'unknown');
        }
    }

    ksort($versions);

    return $versions;
}

/**
 * @return list<array{bundle: string, environments: array<string, bool>, loadable: bool}>
 */
function collectBundleLoadability(string $root): array
{
    $bundles = require $root . '/config/catalog_bundles.php';
    $rows = [];

    foreach ($bundles as $class => $environments) {
        $rows[] = [
            'bundle' => $class,
            'environments' => $environments,
            'loadable' => class_exists($class),
        ];
    }

    return $rows;
}

/**
 * @return array<string, array{version: string, dev: bool}>
 */
function collectLockedPackages(string $root): array
{
    $lockFile = $root . '/composer.lock';
    if (!is_file($lockFile)) {
        return [];
    }

    $decoded = json_decode((string) file_get_contents($lockFile), true, 512, JSON_THROW_ON_ERROR);
    $packages = [];

    foreach ($decoded['packages'] ?? [] as $package) {
        $packages[(string) $package['name']] = [
            'version' => (string) ($package['version'] ?? 'unknown'),
            'dev' => false,
        ];
    }

    foreach ($decoded['packages-dev'] ?? [] as $package) {
        $packages[(string) $package['name']] = [
            'version' => (string) ($package['version'] ?? 'unknown'),
            'dev' => true,
        ];
    }

    ksort($packages);

    return $packages;
}

/**
 * @return array<string, array{name: string, path: string, exists: bool, version: string, dev: bool}>
 */
function collectLockedPackageDirectories(string $root, array $lockedPackages): array
{
    $rows = [];

    foreach ($lockedPackages as $name => $meta) {
        if (!str_contains($name, '/')) {
            continue;
        }

        [$vendor, $package] = explode('/', $name, 2);
        $path = 'vendor/' . $vendor . '/' . $package;
        $rows[$name] = [
            'name' => $name,
            'path' => $path,
            'exists' => is_dir($root . '/' . $path),
            'version' => $meta['version'],
            'dev' => $meta['dev'],
        ];
    }

    ksort($rows);

    return $rows;
}

/**
 * @return array{byPackage: array<string, int>, byStatus: array<string, int>, removedPackageCandidates: list<string>, sample: list<array{path: string, status: string, package: string}>}
 */
function summarizeDirtyVendorPackages(array $dirtyVendor, array $lockedPackages): array
{
    $byPackage = [];
    $byStatus = [];
    $sample = [];

    foreach ($dirtyVendor as $row) {
        $status = $row['status'];
        $byStatus[$status] = ($byStatus[$status] ?? 0) + 1;

        $path = $row['path'];
        $parts = explode('/', $path);
        $package = 'vendor-root';

        if (($parts[0] ?? '') === 'vendor' && isset($parts[1], $parts[2]) && $parts[1] !== 'composer') {
            $package = $parts[1] . '/' . $parts[2];
        } elseif (($parts[0] ?? '') === 'vendor' && ($parts[1] ?? '') === 'composer') {
            $package = 'composer-runtime';
        }

        $byPackage[$package] = ($byPackage[$package] ?? 0) + 1;

        if (count($sample) < 50) {
            $sample[] = [
                'path' => $path,
                'status' => $status,
                'package' => $package,
            ];
        }
    }

    arsort($byPackage);
    ksort($byStatus);

    $removedPackageCandidates = [];
    foreach (array_keys($byPackage) as $package) {
        if ($package === 'composer-runtime' || $package === 'vendor-root') {
            continue;
        }

        if (!isset($lockedPackages[$package])) {
            $removedPackageCandidates[] = $package;
        }
    }
    sort($removedPackageCandidates);

    return [
        'byPackage' => $byPackage,
        'byStatus' => $byStatus,
        'removedPackageCandidates' => $removedPackageCandidates,
        'sample' => $sample,
    ];
}

$packageVersions = collectInstalledVersions($root, [
    'api-platform/doctrine-orm',
    'api-platform/graphql',
    'api-platform/symfony',
    'doctrine/dbal',
    'doctrine/doctrine-bundle',
    'doctrine/orm',
    'nelmio/api-doc-bundle',
    'phpunit/phpunit',
    'symfony/framework-bundle',
    'symfony/http-client',
    'symfony/messenger',
    'symfony/monolog-bundle',
    'symfony/panther',
    'symfony/rate-limiter',
    'symfony/runtime',
    'symfony/security-bundle',
    'symfony/twig-bundle',
]);

$bundleLoadability = collectBundleLoadability($root);
$dirtyComposer = collectGitStatus($root, 'composer.json');
$dirtyLock = collectGitStatus($root, 'composer.lock');
$dirtyVendor = collectGitStatus($root, 'vendor');
$lockedPackages = collectLockedPackages($root);
$lockedPackageDirectories = collectLockedPackageDirectories($root, $lockedPackages);
$missingLockedPackages = array_values(array_filter(
    $lockedPackageDirectories,
    static fn (array $row): bool => $row['exists'] === false,
));
$dirtyVendorSummary = summarizeDirtyVendorPackages($dirtyVendor, $lockedPackages);

$report = [
    'generatedAt' => date(DATE_ATOM),
    'fingerprints' => [
        hashFileIfPresent($root, 'composer.json'),
        hashFileIfPresent($root, 'composer.lock'),
        hashFileIfPresent($root, 'vendor/composer/autoload_psr4.php'),
        hashFileIfPresent($root, 'vendor/composer/autoload_static.php'),
        hashFileIfPresent($root, 'vendor/composer/installed.php'),
        hashFileIfPresent($root, 'vendor/composer/installed.json'),
    ],
    'packageVersions' => $packageVersions,
    'bundleLoadability' => $bundleLoadability,
    'lockedPackages' => [
        'count' => count($lockedPackages),
        'missingDirectoriesCount' => count($missingLockedPackages),
        'missingDirectoriesSample' => array_slice($missingLockedPackages, 0, 50),
    ],
    'gitDirty' => [
        'composerJson' => $dirtyComposer,
        'composerLock' => $dirtyLock,
        'vendorCount' => count($dirtyVendor),
        'vendorSample' => array_slice($dirtyVendor, 0, 50),
    ],
    'vendorDrift' => [
        'dirtyByStatus' => $dirtyVendorSummary['byStatus'],
        'dirtyByPackage' => array_slice($dirtyVendorSummary['byPackage'], 0, 25, true),
        'removedPackageCandidates' => $dirtyVendorSummary['removedPackageCandidates'],
        'sample' => $dirtyVendorSummary['sample'],
    ],
    'summary' => [
        'composerJsonDirty' => $dirtyComposer !== [],
        'composerLockDirty' => $dirtyLock !== [],
        'vendorDirty' => $dirtyVendor !== [],
        'allBundlesLoadable' => array_reduce(
            $bundleLoadability,
            static fn (bool $carry, array $row): bool => $carry && $row['loadable'],
            true,
        ),
        'lockedPackageDirectoriesComplete' => $missingLockedPackages === [],
    ],
];

file_put_contents($out, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
printf(
    "[CatalogDependencyBaselineReport] bundleLoadability=%d vendorDirty=%d missingLockedDirs=%d report=%s\n",
    count($bundleLoadability),
    count($dirtyVendor),
    count($missingLockedPackages),
    str_replace($root . DIRECTORY_SEPARATOR, '', $out),
);
