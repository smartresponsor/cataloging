<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);

$rows = [];
foreach ([
    'src/Repository/CatalogRepository.php',
    'src/GraphQl/CategoryStateProvider.php',
    'src/Repository/Category/CatalogRepository.php',
    'src/GraphQl/Category/CategoryStateProvider.php',
] as $path) {
    $rows[] = [
        'path' => $path,
        'exists' => file_exists($root . '/' . $path),
    ];
}

echo json_encode([
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
