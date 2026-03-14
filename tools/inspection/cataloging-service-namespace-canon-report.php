<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$targets = [
    $root . '/src/Service/Category',
    $root . '/src/ServiceInterface/Category',
    $root . '/src/Entity/Category',
];

$drift = [];
foreach ($targets as $dir) {
    if (!is_dir($dir)) {
        continue;
    }

    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $path = $file->getPathname();
        $content = (string) file_get_contents($path);
        if (!preg_match('/namespace\s+([^;]+);/', $content, $m)) {
            continue;
        }

        $namespace = trim($m[1]);
        $relative = str_replace($root . '/src/', '', substr($path, 0, -4));
        $parts = explode('/', $relative);
        array_pop($parts);
        $expected = 'App\\' . implode('\\', $parts);

        if ($namespace !== $expected) {
            $drift[] = ['path' => str_replace($root . '/', '', $path), 'namespace' => $namespace, 'expected' => $expected];
        }
    }
}

echo json_encode(['drift_count' => count($drift), 'drift' => $drift], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
