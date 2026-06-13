<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

/**
 * CLI: php tools/CategoryEntity/rescue/tools_linter_category_mirror_check.php <project-root>
 * Ensures <Layer> <-> <LayerInterface> mirrors exist for CategoryEntity-related classes.
 */

$root = $argv[1] ?? getcwd();
$layers = ['Entity', 'Service', 'Repository', 'Policy', 'Event', 'ValueObject'];
$fail = 0;

foreach ($layers as $layer) {
    $categoryRoot = $root . "/src/{$layer}";
    if (!is_dir($categoryRoot)) {
        continue;
    }

    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($categoryRoot));
    foreach ($iter as $file) {
        if (!$file->isFile() || substr($file->getFilename(), -4) !== '.php') {
            continue;
        }

        $nameEntity = basename($file->getPathname(), '.php');
        $iface = $root . "/src/{$layer}Interface/{$nameEntity}Interface.php";
        if (!file_exists($iface) && $layer !== 'Event') {
            fwrite(STDERR, "Mirror missing for {$layer} {$nameEntity}: {$iface}\n");
            ++$fail;
        }
    }
}

exit($fail > 0 ? 1 : 0);
