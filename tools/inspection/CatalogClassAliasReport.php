<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$rows = [];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = str_replace($root . '/', '', $file->getPathname());
    $content = (string) file_get_contents($file->getPathname());
    $count = substr_count($content, 'class_alias(');

    if ($count > 0) {
        $rows[] = ['path' => $path, 'classAliasCount' => $count];
    }
}

echo json_encode([
    'file_count' => count($rows),
    'class_alias_count' => array_sum(array_column($rows, 'classAliasCount')),
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
