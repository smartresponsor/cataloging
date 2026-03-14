<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$controllers = [];
$attributeRouteCount = 0;

if (is_dir($root . '/src/Controller')) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src/Controller'));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $path = str_replace($root . '/', '', $file->getPathname());
        $content = (string) file_get_contents($file->getPathname());
        $count = substr_count($content, '#[Route(');
        $attributeRouteCount += $count;
        $controllers[] = ['path' => $path, 'attributeRouteCount' => $count];
    }
}

$routeFiles = [];
if (is_dir($root . '/config/routes')) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/config/routes'));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }
        $routeFiles[] = str_replace($root . '/', '', $file->getPathname());
    }
}

echo json_encode([
    'controller_count' => count($controllers),
    'attribute_route_count' => $attributeRouteCount,
    'route_file_count' => count($routeFiles),
    'controllers' => $controllers,
    'routeFiles' => $routeFiles,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
