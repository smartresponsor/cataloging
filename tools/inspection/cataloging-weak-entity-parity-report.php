<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$targets = [
    'CategoryLink',
    'CategoryRedirect',
    'CategoryTaxonomy',
    'ProjectionControlEntity',
    'VirtualCategoryEntity',
];

$areas = [
    'migrations' => $root . '/migrations',
    'fixtures' => $root . '/src/DataFixtures',
    'demo' => $root . '/fixtures',
    'publicDemo' => $root . '/public/demo',
    'tests' => $root . '/tests',
];

$report = [];

foreach ($targets as $target) {
    $row = ['entity' => $target];
    foreach ($areas as $name => $dir) {
        $count = 0;
        if (is_dir($dir)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
            foreach ($iterator as $file) {
                if (!$file instanceof SplFileInfo || !$file->isFile()) {
                    continue;
                }

                $content = (string) file_get_contents($file->getPathname());
                if (str_contains($content, $target)) {
                    $count++;
                }
            }
        }
        $row[$name] = $count;
    }
    $report[] = $row;
}

echo json_encode(['rows' => $report], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
