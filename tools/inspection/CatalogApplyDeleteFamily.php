<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

if ($argc < 2) {
    fwrite(STDERR, "Usage: php tools/inspection/catalog-apply-delete-family.php <family> [--execute]\n");
    exit(1);
}

$family = $argv[1];
$execute = in_array('--execute', $argv, true);
$listFile = dirname(__DIR__) . '/inspection/catalog-delete-' . $family . '.txt';
if (!is_file($listFile)) {
    fwrite(STDERR, "Unknown family: {$family}\n");
    exit(1);
}

$command = 'php ' . escapeshellarg(dirname(__DIR__) . '/inspection/catalog-apply-delete-candidate.php') . ' ' . escapeshellarg($listFile);
if ($execute) {
    $command .= ' --execute';
}

passthru($command, $code);
exit($code);
