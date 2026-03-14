<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$fail = false;
$prefix = $root . '/src/Controller/Category/';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($prefix));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $code = (string) file_get_contents($path);
    if (!preg_match('/namespace\s+([^;]+);/', $code, $match)) {
        fwrite(STDERR, "missing namespace: {$path}" . PHP_EOL);
        $fail = true;
        continue;
    }

    $namespace = $match[1];
    if (!str_starts_with($namespace, 'App\\Controller\\Category')) {
        fwrite(STDERR, "non-canonical controller namespace: {$namespace} in {$path}" . PHP_EOL);
        $fail = true;
    }
}

exit($fail ? 1 : 0);
