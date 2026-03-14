<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$shadowRoot = $root . '/public/public';
if (!is_dir($shadowRoot)) {
    fwrite(STDOUT, "cataloging_public_shadow_check: OK\n");
    exit(0);
}

$violations = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($shadowRoot, FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if ($file->isFile()) {
        $violations[] = str_replace($root . '/', '', $file->getPathname());
    }
}

if ($violations === []) {
    fwrite(STDOUT, "cataloging_public_shadow_check: OK\n");
    exit(0);
}

foreach ($violations as $path) {
    fwrite(STDERR, $path . PHP_EOL);
}
exit(1);
