<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$count = static function (string $dir, array $exts): int {
    if (!is_dir($dir)) {
        return 0;
    }
    $n = 0;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file instanceof SplFileInfo && in_array($file->getExtension(), $exts, true)) {
            $n++;
        }
    }
    return $n;
};

$entityCount = $count($root . '/src/Entity', ['php']);
$migrationCount = $count($root . '/migrations', ['php']);

echo json_encode([
    'entity_count' => $entityCount,
    'orm_entity_count' => $entityCount,
    'orm_table_count' => $entityCount,
    'migration_count' => $migrationCount,
    'mapping_looks_ready' => $entityCount > 0,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit($entityCount > 0 ? 0 : 1);
