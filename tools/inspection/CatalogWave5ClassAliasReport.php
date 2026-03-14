<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$rows = [];
$count = 0;

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($it as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $content = (string) file_get_contents($file->getPathname());
    $c = substr_count($content, 'class_alias(');
    if ($c > 0) {
        $rows[] = [
            'path' => str_replace($root . '/', '', $file->getPathname()),
            'classAliasCount' => $c,
        ];
        $count += $c;
    }
}

echo json_encode([
    'file_count' => count($rows),
    'class_alias_count' => $count,
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
