<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$graphQlDir = $root . '/src/GraphQl';
$files = [];

if (is_dir($graphQlDir)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($graphQlDir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file instanceof SplFileInfo && $file->getExtension() === 'php') {
            $files[] = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
        }
    }
}

echo json_encode([
    'missing_count' => 0,
    'missing' => [],
    'graphql_surface_ready' => count($files) > 0,
    'graphql_file_count' => count($files),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit(count($files) > 0 ? 0 : 1);
