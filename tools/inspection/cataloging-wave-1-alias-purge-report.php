<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$rows = [];

foreach (glob($root . '/src/*/Category', GLOB_ONLYDIR) ?: [] as $dir) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $count = 0;
    foreach ($it as $file) {
        if ($file instanceof SplFileInfo && $file->isFile() && $file->getExtension() === 'php') {
            $count++;
        }
    }
    $rows[] = [
        'dir' => str_replace($root . '/', '', $dir),
        'php_file_count' => $count,
    ];
}

echo json_encode([
    'wrapper_dir_count' => count($rows),
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
