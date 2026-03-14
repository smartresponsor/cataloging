<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$graphQlRoot = $root . '/src/GraphQl';
if (!is_dir($graphQlRoot)) {
    fwrite(STDOUT, "cataloging_graphql_root_check: src/GraphQl not found\n");
    exit(0);
}

$violations = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($graphQlRoot, FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }

    $relative = str_replace($root . '/', '', $file->getPathname());
    if (str_starts_with($relative, 'src/GraphQl/schema/')) {
        continue;
    }
    if (substr_count($relative, '/') < 3) {
        $violations[] = $relative;
    }
}

if ($violations === []) {
    fwrite(STDOUT, "cataloging_graphql_root_check: OK\n");
    exit(0);
}

foreach ($violations as $path) {
    fwrite(STDERR, $path . PHP_EOL);
}
exit(1);
