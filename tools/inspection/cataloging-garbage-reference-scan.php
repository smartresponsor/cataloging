<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$targets = [
    'CanonicalPolicyLocale-.php',
    'CategorySitemapGenerator-.php',
    'make-category-f..p.sh',
    'category-f..p-hashes.txt',
];

$skip = [
    $root . DIRECTORY_SEPARATOR . '.git',
    $root . DIRECTORY_SEPARATOR . 'vendor',
];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    $path = $file->getPathname();
    foreach ($skip as $prefix) {
        if (str_starts_with($path, $prefix)) {
            continue 2;
        }
    }
    if (!$file->isFile()) {
        continue;
    }
    $content = @file_get_contents($path);
    if ($content === false) {
        continue;
    }
    foreach ($targets as $target) {
        if (str_contains($content, $target)) {
            echo $target . "	" . substr($path, strlen($root) + 1) . PHP_EOL;
        }
    }
}
