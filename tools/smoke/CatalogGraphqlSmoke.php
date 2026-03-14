<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);

$checks = [
    'src/GraphQl/CategoryChildListResolver.php',
    'src/GraphQl/CategoryAncestorListResolver.php',
    'src/GraphQl/CategoryStateProvider.php',
    'config/graphql/catalog.yaml',
    'config/services_graphql.yaml',
];

$missing = [];
foreach ($checks as $rel) {
    if (!file_exists($root . '/' . $rel)) {
        $missing[] = $rel;
    }
}

echo json_encode([
    'missing_count' => count($missing),
    'missing' => $missing,
    'graphql_surface_ready' => count($missing) === 0,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
