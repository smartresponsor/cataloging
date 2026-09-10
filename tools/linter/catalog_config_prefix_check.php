<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

/**
 * CLI: php tools/linter/catalog_config_prefix_check.php <project-root>
 * Ensures owner-managed YAML config filenames under /config start with "catalog_".
 *
 * Conventional Symfony/vendor bootstrap filenames remain exempt outside the
 * component-owned export directory. Every YAML file in config/component is
 * owned by Cataloging and therefore must carry the catalog_ subject prefix.
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
    'doctrine.yaml',
    'framework.yaml',
    'messenger.yaml',
    'monolog.yaml',
    'nelmio_api_doc.yaml',
    'routes.yaml',
    'security.yaml',
    'services.yaml',
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

    if (str_starts_with($relativePath, 'component/')) {
        if (str_starts_with($nameEntity, 'catalog_')) {
            continue;
        }

        fwrite(STDERR, "Config prefix violation: {$path}\n");
        $fail++;
        continue;
    }

    if (str_starts_with($nameEntity, 'catalog_') || in_array($nameEntity, $allowedNames, true)) {
        continue;
    }

    fwrite(STDERR, "Config prefix violation: {$path}\n");
    $fail++;
}

exit($fail > 0 ? 1 : 0);
