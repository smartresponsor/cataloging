<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$controllers = [];
$routeCount = 0;

$controllerDir = $root . '/src/Controller';
if (is_dir($controllerDir)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllerDir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo || $file->getExtension() !== 'php') {
            continue;
        }
        $content = file_get_contents($file->getPathname());
        $count = substr_count((string) $content, '#[Route(');
        $controllers[] = [
            'path' => str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname()),
            'attributeRouteCount' => $count,
        ];
        $routeCount += $count;
    }
}

$routeFiles = [];
$routeDir = $root . '/config/routes';
if (is_dir($routeDir)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($routeDir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo) {
            continue;
        }
        if (!in_array($file->getExtension(), ['yaml', 'yml'], true)) {
            continue;
        }
        $routeFiles[] = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
    }
}

echo json_encode([
    'controller_count' => count($controllers),
    'attribute_route_count' => $routeCount,
    'route_file_count' => count($routeFiles),
    'controllers' => $controllers,
    'routeFiles' => $routeFiles,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
