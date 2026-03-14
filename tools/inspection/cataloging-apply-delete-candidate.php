<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

if ($argc < 2) {
    fwrite(STDERR, "Usage: php tools/inspection/cataloging-apply-delete-candidate.php <list-file> [--execute]\n");
    exit(1);
}

$listFile = $argv[1];
$execute = in_array('--execute', $argv, true);
$root = dirname(__DIR__, 2);

if (!is_file($listFile)) {
    $candidate = $root . '/' . ltrim($listFile, '/');
    if (!is_file($candidate)) {
        fwrite(STDERR, "List file not found: {$listFile}\n");
        exit(1);
    }
    $listFile = $candidate;
}

$lines = file($listFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lines === false) {
    fwrite(STDERR, "Unable to read list file: {$listFile}\n");
    exit(1);
}

foreach ($lines as $line) {
    $trimmed = trim($line);
    if ($trimmed === '' || str_starts_with($trimmed, '#')) {
        continue;
    }

    $target = $root . '/' . rtrim($trimmed, '/');
    $exists = file_exists($target);
    $kind = is_dir($target) ? 'dir' : 'file';
    fwrite(STDOUT, ($execute ? 'EXECUTE' : 'DRY-RUN') . " {$kind} {$trimmed} " . ($exists ? 'exists' : 'missing') . PHP_EOL);

    if (!$execute || !$exists) {
        continue;
    }

    if (is_dir($target)) {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($it as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($target);
        continue;
    }

    unlink($target);
}
