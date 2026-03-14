<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);

$required = [
    'composer.json',
    'config/bundles.php',
    'config/services.yaml',
    'config/routes/attributes.yaml',
    'config/routes/catalog-move.yaml',
    'src/Kernel.php',
    'phpunit.xml.dist',
];

$missing = [];
foreach ($required as $rel) {
    if (!file_exists($root . '/' . $rel)) {
        $missing[] = $rel;
    }
}

$kernelContent = is_file($root . '/src/Kernel.php')
    ? (string) file_get_contents($root . '/src/Kernel.php')
    : '';

$servicesContent = is_file($root . '/config/services.yaml')
    ? (string) file_get_contents($root . '/config/services.yaml')
    : '';

$routeAttrContent = is_file($root . '/config/routes/attributes.yaml')
    ? (string) file_get_contents($root . '/config/routes/attributes.yaml')
    : '';

echo json_encode([
    'missing_count' => count($missing),
    'missing' => $missing,
    'kernel_imports_config' => str_contains($kernelContent, 'config/packages') || str_contains($kernelContent, 'config/services'),
    'services_autowire' => str_contains($servicesContent, 'autowire: true'),
    'services_autoconfigure' => str_contains($servicesContent, 'autoconfigure: true'),
    'attribute_route_import' => str_contains($routeAttrContent, 'type: attribute'),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
