<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$entityDir = $root . '/src/Entity';
$migrationDir = $root . '/migrations';

$entityCount = 0;
$ormEntityCount = 0;
$ormTableCount = 0;

if (is_dir($entityDir)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($entityDir));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $entityCount++;
        $content = (string) file_get_contents($file->getPathname());
        if (str_contains($content, '#[ORM\Entity]')) {
            $ormEntityCount++;
        }
        if (str_contains($content, '#[ORM\Table(')) {
            $ormTableCount++;
        }
    }
}

$migrationCount = 0;
if (is_dir($migrationDir)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($migrationDir));
    foreach ($it as $file) {
        if ($file instanceof SplFileInfo && $file->isFile() && $file->getExtension() === 'php') {
            $migrationCount++;
        }
    }
}

echo json_encode([
    'entity_count' => $entityCount,
    'orm_entity_count' => $ormEntityCount,
    'orm_table_count' => $ormTableCount,
    'migration_count' => $migrationCount,
    'mapping_looks_ready' => $ormEntityCount > 0 && $migrationCount > 0,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
