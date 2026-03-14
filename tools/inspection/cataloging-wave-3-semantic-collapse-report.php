<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);

$checks = [
    'src/Event/CategoryMoved.php',
    'src/Event/Category/CategoryMoved.php',
    'src/GraphQl/CategoryQuery.php',
    'src/GraphQl/Category/CategoryQuery.php',
    'src/Projection/CategoryProjectionRunner.php',
    'src/Projection/Category/CategoryProjectionRunner.php',
    'src/Security/CategoryVoter.php',
    'src/Security/Category/CategoryVoter.php',
];

$rows = [];
foreach ($checks as $path) {
    $rows[] = [
        'path' => $path,
        'exists' => file_exists($root . '/' . $path),
    ];
}

echo json_encode([
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
