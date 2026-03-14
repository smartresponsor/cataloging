<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$fail = false;
$legacyList = [
    'namespace App\\Http\\Category;',
    'namespace SmartResponsor\\Category\\Http;',
    'namespace SmartResponsor\\Category\\Api\\Graphql;',
];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $code = (string) file_get_contents($path);
    foreach ($legacyList as $needle) {
        if (str_contains($code, $needle)) {
            fwrite(STDERR, "legacy namespace token: {$needle} in {$path}" . PHP_EOL);
            $fail = true;
        }
    }
}

exit($fail ? 1 : 0);
