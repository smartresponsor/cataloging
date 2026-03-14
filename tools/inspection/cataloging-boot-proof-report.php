<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);

$files = [
    'composer.json',
    'config/bundles.php',
    'config/services.yaml',
    'config/routes/attributes.yaml',
    'config/routes/catalog-move.yaml',
    'src/Kernel.php',
    'phpunit.xml.dist',
];

$present = [];
$missing = [];
foreach ($files as $rel) {
    if (file_exists($root . '/' . $rel)) {
        $present[] = $rel;
    } else {
        $missing[] = $rel;
    }
}

echo json_encode([
    'present_count' => count($present),
    'missing_count' => count($missing),
    'present' => $present,
    'missing' => $missing,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
