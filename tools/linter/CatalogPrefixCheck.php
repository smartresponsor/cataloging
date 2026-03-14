<?php
declare(strict_types=1);

/**
 * CLI: php tools/linter/CatalogPrefixCheck.php <project-root>
 * Flags component-facing files that still start with `category-` where `catalog-` is expected.
 */
$root = $argv[1] ?? getcwd();
$fail = 0;
$paths = [
    $root . '/docs',
    $root . '/api',
    $root . '/ops',
    $root . '/report',
    $root . '/config',
    $root . '/tools',
];

foreach ($paths as $path) {
    if (!is_dir($path)) {
        continue;
    }

    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iter as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }

        $name = $file->getFilename();
        if (str_starts_with($name, 'category-') || str_starts_with($name, 'category_')) {
            fwrite(STDERR, "Catalog prefix expected, found legacy category prefix: " . $file->getPathname() . "
");
            $fail++;
        }
    }
}

exit($fail > 0 ? 1 : 0);
