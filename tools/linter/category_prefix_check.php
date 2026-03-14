<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = $argv[1] ?? getcwd();
$root = is_string($root) ? $root : getcwd();
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
$fail = 0;

foreach ($iterator as $file) {
    if (!$file->isFile() || substr($file->getFilename(), -4) !== '.php') {
        continue;
    }

    $path = str_replace('\\', '/', $file->getPathname());
    if (!preg_match('~/Category/.*\.php$~', $path)) {
        continue;
    }

    $name = basename($path, '.php');
    if (!preg_match('/^Category([A-Z].*)?$/', $name)) {
        fwrite(STDERR, 'Prefix violation: ' . $path . PHP_EOL);
        $fail++;
    }
}

exit($fail > 0 ? 1 : 0);
