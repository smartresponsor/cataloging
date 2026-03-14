<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$out = [];
$patterns = [
                    ];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($it as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $content = file_get_contents($file->getPathname()) ?: '';
    if (!preg_match('/^namespace\s+([^;]+);/m', $content, $m)) {
        continue;
    }
    $namespace = trim($m[1]);
    foreach ($patterns as $prefix => $kind) {
        if (str_starts_with($namespace, $prefix)) {
            $out[] = [
                'kind' => $kind,
                'file' => substr($file->getPathname(), strlen($root) + 1),
                'namespace' => $namespace,
            ];
            break;
        }
    }
}
foreach ($out as $row) {
    echo json_encode($row, JSON_UNESCAPED_SLASHES) . PHP_EOL;
}
