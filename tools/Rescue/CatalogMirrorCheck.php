<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = $argv[1] ?? getcwd();
if (!is_string($root) || $root === '') {
    fwrite(STDERR, "Missing project root.\n");
    exit(1);
}

$layers = ['Entity', 'Service', 'Repository', 'Policy', 'Event', 'ValueObject'];
$fail = 0;

foreach ($layers as $layer) {
    $left = $root . '/src/' . $layer;
    $right = $root . '/src/' . $layer . 'Interface';

    if (is_dir($left) xor is_dir($right)) {
        fwrite(STDERR, "Mirror mismatch for {$layer}.\n");
        $fail = 1;
    }
}

exit($fail);
