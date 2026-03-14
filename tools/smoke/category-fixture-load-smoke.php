<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);

$checks = [
    'src/DataFixtures/CategoryFixtures.php',
    'fixtures/Category/parity.yaml',
    'public/demo/category-parity.json',
];

$missing = [];
foreach ($checks as $rel) {
    if (!file_exists($root . '/' . $rel)) {
        $missing[] = $rel;
    }
}

$fixtureContent = is_file($root . '/src/DataFixtures/CategoryFixtures.php')
    ? (string) file_get_contents($root . '/src/DataFixtures/CategoryFixtures.php')
    : '';

$fixtureSignal = [
    'CategoryTaxonomy',
    'CategoryLink',
    'CategoryRedirect',
    'ProjectionControlEntity',
    'VirtualCategoryEntity',
];

$fixtureCoverage = [];
foreach ($fixtureSignal as $needle) {
    $fixtureCoverage[$needle] = str_contains($fixtureContent, $needle);
}

$demoPath = $root . '/public/demo/category-parity.json';
$demoJsonOk = false;
if (is_file($demoPath)) {
    $raw = file_get_contents($demoPath);
    $decoded = is_string($raw) ? json_decode($raw, true) : null;
    $demoJsonOk = is_array($decoded);
}

echo json_encode([
    'missing_count' => count($missing),
    'missing' => $missing,
    'fixture_coverage' => $fixtureCoverage,
    'demo_json_ok' => $demoJsonOk,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
