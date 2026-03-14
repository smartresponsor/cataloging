<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);

$controllerCount = 0;
$attributeRouteCount = 0;
if (is_dir($root . '/src/Controller')) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src/Controller'));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }
        $controllerCount++;
        $content = (string) file_get_contents($file->getPathname());
        $attributeRouteCount += substr_count($content, '#[Route(');
    }
}

$routeFileCount = 0;
if (is_dir($root . '/config/routes')) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/config/routes'));
    foreach ($it as $file) {
        if ($file instanceof SplFileInfo && $file->isFile()) {
            $routeFileCount++;
        }
    }
}

$graphQlCount = 0;
if (is_dir($root . '/src/GraphQl')) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src/GraphQl'));
    foreach ($it as $file) {
        if ($file instanceof SplFileInfo && $file->isFile() && $file->getExtension() === 'php') {
            $graphQlCount++;
        }
    }
}

echo json_encode([
    'controller_count' => $controllerCount,
    'attribute_route_count' => $attributeRouteCount,
    'route_file_count' => $routeFileCount,
    'graphql_php_count' => $graphQlCount,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
