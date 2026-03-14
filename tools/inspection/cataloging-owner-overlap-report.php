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

    $basename = $file->getBasename();
    $groups[$basename][] = str_replace($root . '/', '', $file->getPathname());
}

$rows = [];
foreach ($groups as $basename => $paths) {
    if (count($paths) > 1) {
        sort($paths);
        $rows[] = ['basename' => $basename, 'count' => count($paths), 'paths' => $paths];
    }
}

usort($rows, static fn(array $a, array $b): int => $b['count'] <=> $a['count']);

echo json_encode([
    'duplicate_group_count' => count($rows),
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
