<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$fail = false;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $code = (string) file_get_contents($path);
    if (str_contains($code, 'namespace SmartResponsor\\')) {
        fwrite(STDERR, 'legacy SmartResponsor namespace in ' . $path . PHP_EOL);
        $fail = true;
    }
}

exit($fail ? 1 : 0);
