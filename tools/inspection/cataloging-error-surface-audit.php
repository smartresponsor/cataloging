<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$targets = [$root . '/src', $root . '/tests', $root . '/tools'];
$rows = [];

$patterns = [
    '@mkdir' => '@mkdir',
    'unchecked_json_decode' => 'json_decode(',
    'file_get_contents' => 'file_get_contents(',
    'file_put_contents' => 'file_put_contents(',
    'pdo_prepare' => '->prepare(',
];

foreach ($targets as $target) {
    if (!is_dir($target)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $path = $file->getPathname();
        $content = (string) file_get_contents($path);

        foreach ($patterns as $name => $needle) {
            if (!str_contains($content, $needle)) {
                continue;
            }

            $rows[] = [
                'path' => str_replace($root . '/', '', $path),
                'pattern' => $name,
                'needle' => $needle,
                'hasLogger' => str_contains($content, 'LoggerInterface') || str_contains($content, 'NullLogger'),
                'hasTryCatch' => str_contains($content, 'try {') && str_contains($content, 'catch'),
                'usesJsonThrow' => str_contains($content, 'JSON_THROW_ON_ERROR'),
            ];
        }
    }
}

echo json_encode([
    'row_count' => count($rows),
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
