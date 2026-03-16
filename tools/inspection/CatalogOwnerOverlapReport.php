<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$src = $root . '/src';
$groups = [];

if (is_dir($src)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo || $file->getExtension() !== 'php') {
            continue;
        }
        $base = $file->getBasename();
        $groups[$base][] = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
    }
}

$rows = [];
foreach ($groups as $base => $paths) {
    if (count($paths) > 1) {
        $rows[] = [
            'basename' => $base,
            'count' => count($paths),
            'paths' => array_values($paths),
        ];
    }
}

usort($rows, static fn(array $a, array $b): int => $b['count'] <=> $a['count']);

echo json_encode([
    'duplicate_group_count' => count($rows),
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
