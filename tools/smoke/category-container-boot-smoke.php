<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$services = $root . '/config/services.yaml';
$kernel = $root . '/src/Kernel.php';

$result = [
    'missing_count' => 0,
    'missing' => [],
    'kernel_imports_config' => file_exists($kernel),
    'services_autowire' => file_exists($services),
    'services_autoconfigure' => file_exists($services),
    'attribute_route_import' => file_exists($root . '/config/routes'),
];

foreach ([
    'src/Kernel.php' => $kernel,
    'config/services.yaml' => $services,
] as $label => $path) {
    if (!file_exists($path)) {
        $result['missing'][] = $label;
    }
}

$result['missing_count'] = count($result['missing']);

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit($result['missing_count'] === 0 ? 0 : 1);
