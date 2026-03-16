<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);

$controllerCount = 0;
$graphqlCount = 0;
$routeFileCount = 0;

foreach (['src/Controller' => 'php', 'src/GraphQl' => 'php', 'config/routes' => 'yaml'] as $rel => $kind) {
    $dir = $root . '/' . $rel;
    if (!is_dir($dir)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo) {
            continue;
        }
        if ($rel === 'src/Controller' && $file->getExtension() === 'php') {
            $controllerCount++;
        }
        if ($rel === 'src/GraphQl' && $file->getExtension() === 'php') {
            $graphqlCount++;
        }
        if ($rel === 'config/routes' && in_array($file->getExtension(), ['yaml', 'yml'], true)) {
            $routeFileCount++;
        }
    }
}

echo json_encode([
    'controller_count' => $controllerCount,
    'route_file_count' => $routeFileCount,
    'graphql_php_count' => $graphqlCount,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
