<?php
declare(strict_types=1);

namespace App\Category;

<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp


/**
 * CLI: php tools/linter/category_prefix_check.php <project-root>
 * Ensures classes under Category domain start with 'Category'.
 */
$root = $argv[1] ?? getcwd();
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$fail = 0;
foreach ($rii as $file) {
    if (!$file->isFile()) continue;
    if (!preg_match('~src/.*/Category/.*\.php$~', $file->getPathname())) continue;
    $name = basename($file->getPathname(), '.php');
    if (!preg_match('/^Category([A-Z].*)?$|^Category$/', $name)) {
        fwrite(STDERR, "Prefix violation: {$file->getPathname()}\n");
        $fail++;
    }
}
exit($fail > 0 ? 1 : 0);
