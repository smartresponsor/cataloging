<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

/**
 * CLI: php tools/linter/category_prefix_check.php <project-root>
 * Ensures classes under CategoryEntity responsibility paths start with 'CategoryEntity'.
 */
$root = $argv[1] ?? getcwd();
if (!is_string($root) || !is_dir($root)) {
    fwrite(STDERR, "Invalid project root: {$root}\n");
    exit(2);
}

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$fail = 0;

foreach ($rii as $file) {
    if (!$file->isFile()) {
        continue;
    }

    $path = str_replace('\\', '/', $file->getPathname());
    if (preg_match('~(?:^|/)src/.*/CategoryEntity/[^/]+\.php$~', $path) !== 1) {
        continue;
    }

    $nameEntity = basename($path, '.php');
    if (preg_match('/^CategoryEntity([A-Z].*)?$/', $nameEntity) !== 1) {
        fwrite(STDERR, "Prefix violation: {$path}\n");
        $fail++;
    }
}

exit($fail > 0 ? 1 : 0);
