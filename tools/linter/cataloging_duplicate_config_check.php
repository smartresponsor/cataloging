<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = rtrim($argv[1] ?? getcwd(), DIRECTORY_SEPARATOR);
$source = $root . DIRECTORY_SEPARATOR . 'config';
$duplicateRoot = $source . DIRECTORY_SEPARATOR . 'config';
$fail = 0;

if (!is_dir($duplicateRoot)) {
    exit(0);
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($duplicateRoot));
foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }

    $duplicatePath = str_replace('\\', '/', $file->getPathname());
    $relative = substr($duplicatePath, strlen(str_replace('\\', '/', $duplicateRoot)) + 1);
    $primary = str_replace('\\', '/', $source . DIRECTORY_SEPARATOR . $relative);
    if (is_file($primary)) {
        fwrite(STDERR, "Duplicate config candidate: {$duplicatePath} mirrors {$primary}
");
        ++$fail;
    }
}

exit($fail > 0 ? 1 : 0);
