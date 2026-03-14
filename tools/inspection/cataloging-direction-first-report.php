<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$checks = ['src/Service/Category', 'src/ServiceInterface/Category', 'src/Entity/Category'];
$hit = [];
foreach ($checks as $path) {
    if (is_dir($root . '/' . $path)) {
        $hit[] = $path;
    }
}
echo json_encode(['forbidden_wrapper_dir_count' => count($hit), 'forbidden_wrapper_dir' => $hit], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
