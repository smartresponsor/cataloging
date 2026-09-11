<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

/**
 * CLI: php tools/linter/catalog_config_prefix_check.php <project-root>
 * Ensures owner-managed YAML config filenames under /config start with "catalog_".
 *
 * Symfony/framework-facing config files are exempt because their naming is
 * primarily governed by Symfony and bundle integration conventions.
 */
$root = $argv[1] ?? getcwd();
if (!is_string($root) || !is_dir($root)) {
    fwrite(STDERR, "Invalid project root: {$root}\n");
    exit(2);
}

$configRoot = rtrim($root, '/\\').'/config';
if (!is_dir($configRoot)) {
    fwrite(STDERR, "Config directory not found: {$configRoot}\n");
    exit(1);
}

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configRoot));
$fail = 0;
$allowedNames = [
    'api_platform.yaml',
    'component.yaml',
    'config_tools.yaml',
    'doctrine.yaml',
    'env.yaml',
    'framework.yaml',
    'messenger.yaml',
    'monolog.yaml',
    'nelmio_api_doc.yaml',
    'routes.yaml',
    'runtime.yaml',
    'security.yaml',
    'services.yaml',
    'smoke.yaml',
    'twig.yaml',
    'web_profiler.yaml',
];

foreach ($rii as $file) {
    if (!$file->isFile()) {
        continue;
    }

    $path = str_replace('\\', '/', $file->getPathname());
    if (preg_match('~\.ya?ml$~', $path) !== 1) {
        continue;
    }

    $nameEntity = basename($path);
    $relativePath = ltrim(substr($path, strlen(str_replace('\\', '/', $configRoot))), '/');

    if (str_starts_with($relativePath, 'component/') && in_array($nameEntity, $allowedNames, true)) {
        continue;
    }

    if (str_starts_with($nameEntity, 'catalog_') || in_array($nameEntity, $allowedNames, true)) {
        continue;
    }

    fwrite(STDERR, "Config prefix violation: {$path}\n");
    $fail++;
}

exit($fail > 0 ? 1 : 0);
