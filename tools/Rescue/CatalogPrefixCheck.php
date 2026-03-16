<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = $argv[1] ?? getcwd();
if (!is_string($root) || $root === '') {
    fwrite(STDERR, "Missing project root.\n");
    exit(1);
}

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$fail = 0;

foreach ($rii as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile()) {
        continue;
    }
    $path = str_replace('\\', '/', $file->getPathname());
    if (!preg_match('~src/.*/tests/.*\.php$~', $path)) {
        continue;
    }

    $content = file_get_contents($path);
    if (!is_string($content)) {
        continue;
    }

    if (preg_match('/\b(class|interface|trait)\s+([A-Za-z_][A-Za-z0-9_]*)/', $content, $m)) {
        $name = $m[2];
        if (!str_starts_with($name, 'tests') && !str_starts_with($name, 'Catalogtests')) {
            fwrite(STDERR, "Non-canonical class prefix in {$path}: {$name}\n");
            $fail = 1;
        }
    }
}

exit($fail);
