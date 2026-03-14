<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$targets = [
];

$exit = 0;
foreach ($targets as $path => $needle) {
    $full = $root . DIRECTORY_SEPARATOR . $path;
    if (!is_dir($full)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($full));
    foreach ($iterator as $item) {
        if (!$item->isFile() || $item->getExtension() !== 'php') {
            continue;
        }
        $content = (string) file_get_contents($item->getPathname());
        if (str_contains($content, $needle)) {
            fwrite(STDERR, '[namespace-drift] ' . substr($item->getPathname(), strlen($root) + 1) . PHP_EOL);
            $exit = 1;
        }
    }
}

exit($exit);
