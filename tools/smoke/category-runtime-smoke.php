<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);

$checks = [
    'composer.json',
    'config/bundles.php',
    'config/services.yaml',
    'config/routes/attributes.yaml',
    'config/routes/category-move.yaml',
    'src/Kernel.php',
    'phpunit.xml.dist',
];

$missing = [];
foreach ($checks as $rel) {
    if (!file_exists($root . '/' . $rel)) {
        $missing[] = $rel;
    }
}

$controllers = is_dir($root . '/src/Controller') ? iterator_count(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src/Controller'))) : 0;
$routes = is_dir($root . '/config/routes') ? iterator_count(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/config/routes'))) : 0;

echo json_encode([
    'missing_count' => count($missing),
    'missing' => $missing,
    'controller_entry_count' => $controllers,
    'route_file_entry_count' => $routes,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
