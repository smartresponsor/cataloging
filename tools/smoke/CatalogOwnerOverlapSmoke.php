<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$src = $root . '/src';
$groups = [];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src));
foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $groups[$file->getBasename()][] = str_replace($root . '/', '', $file->getPathname());
}

$duplicates = [];
foreach ($groups as $basename => $paths) {
    if (count($paths) > 1) {
        $duplicates[$basename] = $paths;
    }
}

echo json_encode([
    'duplicate_group_count' => count($duplicates),
    'duplicates' => $duplicates,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
