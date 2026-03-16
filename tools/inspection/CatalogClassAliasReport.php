<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$src = $root . '/src';
$rows = [];

if (is_dir($src)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo || $file->getExtension() !== 'php') {
            continue;
        }
        $content = file_get_contents($file->getPathname());
        $count = substr_count((string) $content, 'class_alias(');
        if ($count > 0) {
            $rows[] = [
                'path' => str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname()),
                'classAliasCount' => $count,
            ];
        }
    }
}

echo json_encode([
    'file_count' => count($rows),
    'class_alias_count' => array_sum(array_column($rows, 'classAliasCount')),
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
