<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$srcPhp = 0;
$classAliasCount = 0;
$duplicate = [];

$groups = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($it as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $srcPhp++;
    $content = (string) file_get_contents($file->getPathname());
    $classAliasCount += substr_count($content, 'class_alias(');
    $groups[$file->getBasename()][] = str_replace($root . '/', '', $file->getPathname());
}

foreach ($groups as $basename => $paths) {
    if (count($paths) > 1) {
        $duplicate[] = ['basename' => $basename, 'count' => count($paths)];
    }
}

echo json_encode([
    'src_php_count' => $srcPhp,
    'class_alias_count' => $classAliasCount,
    'duplicate_group_count' => count($duplicate),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
