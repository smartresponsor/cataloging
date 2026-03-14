<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$bad = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($it as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $content = file_get_contents($file->getPathname()) ?: '';
    if (str_contains($content, 'namespace App\\HttpInterface\\') || str_contains($content, 'use App\\HttpInterface\\')) {
        $bad[] = substr($file->getPathname(), strlen($root) + 1);
    }
}
if ($bad !== []) {
    fwrite(STDERR, "Legacy HttpInterface namespace drift detected:\n" . implode("\n", $bad) . "\n");
    exit(1);
}
echo "OK\n";
