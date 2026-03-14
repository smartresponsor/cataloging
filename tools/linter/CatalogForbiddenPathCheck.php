<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = $argv[1] ?? getcwd();
$root = is_string($root) ? $root : getcwd();
$forbidden = [
    'src/Domain',
    'src/DomainInterface',
    'src/Adapter',
    'src/Infra',
    'src/InfraInterface',
];
$fail = 0;

foreach ($forbidden as $path) {
    $fullPath = $root . '/' . $path;
    if (is_dir($fullPath)) {
        fwrite(STDERR, 'Forbidden path present: ' . $path . PHP_EOL);
        $fail++;
    }
}

exit($fail > 0 ? 1 : 0);
