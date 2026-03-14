<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Util;

final class RotatingFileWriter
{
    public function __construct(
        private readonly string $path,
        private readonly int $maxBytes = 5242880,
        private readonly int $maxFiles = 3,
    ) {
    }

    public function write(string $line): void
    {
        $dir = dirname($this->path);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('The log directory could not be created.');
        }

        $this->rotateIfNeeded();

        $written = file_put_contents($this->path, $line, FILE_APPEND | LOCK_EX);
        if (false === $written) {
            throw new \RuntimeException('The log file could not be written.');
        }
    }

    private function rotateIfNeeded(): void
    {
        if (!file_exists($this->path)) {
            return;
        }

        $size = filesize($this->path);
        if (!is_int($size) || $size < $this->maxBytes) {
            return;
        }

        for ($i = $this->maxFiles - 1; $i >= 1; --$i) {
            $old = $this->path.'.'.$i;
            $new = $this->path.'.'.($i + 1);

            if (file_exists($old) && !rename($old, $new)) {
                throw new \RuntimeException('A rotated log file could not be renamed.');
            }
        }

        if (!rename($this->path, $this->path.'.1')) {
            throw new \RuntimeException('The current log file could not be rotated.');
        }
    }
}
