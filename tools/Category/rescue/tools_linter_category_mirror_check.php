<?php
declare(strict_types=1);

namespace App\Category;

<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

/**
 * CLI: php tools/linter/category_mirror_check.php <project-root>
 * Ensures <Layer> <-> <Layer Interface> mirrors exist for Category domain.
 */
$root = $argv[1] ?? getcwd();
$layers = ['Entity','Service','Repository','Policy','Event','ValueObject'];
$fail = 0;
foreach ($layers as $layer) {
    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . "/src/{$layer}/Category"));
    foreach ($iter as $file) {
        if (!$file->isFile() || substr($file->getFilename(), -4) !== '.php') continue;
        $name = basename($file->getPathname(), '.php');
        $iface = $root . "/src/{$layer}Interface/Category/{$name}Interface.php";
        if (!file_exists($iface) and $layer !== 'Event') { // events may be fire-and-forget; interface optional
            fwrite(STDERR, "Mirror missing for {$layer} {$name}: {$iface}\n");
            $fail++;
        }
    }
}
exit($fail > 0 ? 1 : 0);
