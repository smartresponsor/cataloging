<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-runtime-proof-report.json';

$checks = [
    'kernel' => [$root . '/src/Kernel.php'],
    'frontController' => [$root . '/public/index.php'],
    'openApi' => [$root . '/api/category-openapi.yaml'],
    'graphqlConfig' => [$root . '/config/catalog_services_graphql.yaml', $root . '/config/graphql/catalog_category.yaml'],
    'doctrineConfig' => [$root . '/config/packages/catalog_doctrine.yaml'],
    'fixtureClass' => [$root . '/src/DataFixtures/CategoryFixtures.php'],
];
$items = [];
foreach ($checks as $label => $paths) {
    $existingPaths = array_values(array_filter($paths, static fn (string $path): bool => file_exists($path)));
    $items[] = [
        'check' => $label,
        'paths' => array_map(static fn (string $path): string => str_replace($root . DIRECTORY_SEPARATOR, '', $path), $paths),
        'exists' => $existingPaths !== [],
    ];
}
file_put_contents($out, json_encode(['generatedAt' => date(DATE_ATOM), 'items' => $items], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo sprintf("[CatalogRuntimeProofReport] %d runtime proof rows written to %s\n", count($items), str_replace($root . DIRECTORY_SEPARATOR, '', $out));
exit(0);
