<?php

declare(strict_types=1);

$roots = [
    __DIR__.'/../../src',
    __DIR__.'/../../tests',
    __DIR__.'/../../tools',
];

$phpBinary = PHP_BINARY;
$exitCode = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__.'/../../', FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo) {
        continue;
    }

    if (!$file->isFile()) {
        continue;
    }

    if ('php' !== strtolower($file->getExtension())) {
        continue;
    }

    $path = $file->getPathname();

    $isInAllowedRoot = false;

    foreach ($roots as $root) {
        if (str_starts_with($path, $root.DIRECTORY_SEPARATOR) || $path === $root) {
            $isInAllowedRoot = true;
            break;
        }
    }

    if (!$isInAllowedRoot) {
        continue;
    }

    $command = escapeshellarg($phpBinary).' -l '.escapeshellarg($path);
    passthru($command, $fileExitCode);

    if (0 !== $fileExitCode) {
        $exitCode = $fileExitCode;
    }
}

exit($exitCode);
