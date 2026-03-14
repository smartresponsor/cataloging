<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$shadowRoot = $root . '/config/config';
$execute = in_array('--execute', $argv, true);

if (!is_dir($shadowRoot)) {
    fwrite(STDOUT, "catalog-shadow-config-prune: no shadow config tree\n");
    exit(0);
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($shadowRoot, FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }

    $shadowPath = $file->getPathname();
    $relative = substr($shadowPath, strlen($shadowRoot) + 1);
    $ownerPath = $root . '/config/' . $relative;
    $state = 'shadow-only';
    if (is_file($ownerPath)) {
        $state = hash_file('sha256', $shadowPath) === hash_file('sha256', $ownerPath) ? 'shadow-duplicate' : 'shadow-diverged';
    }

    fwrite(STDOUT, ($execute ? 'EXECUTE' : 'DRY-RUN') . " {$state} {$relative}\n");
    if ($execute && $state === 'shadow-duplicate') {
        unlink($shadowPath);
    }
}
