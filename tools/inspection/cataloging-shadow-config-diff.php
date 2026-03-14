<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$ownerRoot = $root . DIRECTORY_SEPARATOR . 'config';
$shadowRoot = $ownerRoot . DIRECTORY_SEPARATOR . 'config';

if (!is_dir($shadowRoot)) {
    fwrite(STDERR, "shadow config tree not found\n");
    exit(0);
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($shadowRoot, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }
    $shadowPath = $file->getPathname();
    $relative = substr($shadowPath, strlen($shadowRoot) + 1);
    $ownerPath = $ownerRoot . DIRECTORY_SEPARATOR . $relative;
    $status = 'shadow-only';
    if (is_file($ownerPath)) {
        $status = hash_file('sha256', $shadowPath) === hash_file('sha256', $ownerPath)
            ? 'shadow-duplicate'
            : 'shadow-diverged';
    }
    echo $status . "\t" . $relative . PHP_EOL;
}
