<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$noise = [];

$exactDirs = ['.git', '.idea'];
foreach ($exactDirs as $dir) {
    if (is_dir($root . '/' . $dir)) {
        $noise[] = $dir . '/';
    }
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/tools', FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }

    $path = str_replace($root . '/', '', $file->getPathname());
    $ext = strtolower($file->getExtension());
    if (in_array($ext, ['zip', 'tar', 'tgz', 'gz'], true)) {
        $noise[] = $path;
    }
}

if ($noise === []) {
    fwrite(STDOUT, "cataloging_repo_artifact_noise_check: OK\n");
    exit(0);
}

sort($noise);
foreach ($noise as $path) {
    fwrite(STDERR, $path . PHP_EOL);
}
exit(1);
