<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);

$checks = [
    'src/DataFixtures/CategoryFixtures.php',
    'fixtures/Category/parity.yaml',
    'public/demo/category-parity.json',
    'migrations/Version20260312090000_category_parity_entities.php',
];

$missing = [];
foreach ($checks as $rel) {
    if (!file_exists($root . '/' . $rel)) {
        $missing[] = $rel;
    }
}

$demoJsonOk = false;
$demoPath = $root . '/public/demo/category-parity.json';
if (is_file($demoPath)) {
    $raw = file_get_contents($demoPath);
    $decoded = is_string($raw) ? json_decode($raw, true) : null;
    $demoJsonOk = is_array($decoded);
}

echo json_encode([
    'missing_count' => count($missing),
    'missing' => $missing,
    'demo_json_ok' => $demoJsonOk,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
