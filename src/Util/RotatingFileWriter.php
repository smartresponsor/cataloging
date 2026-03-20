<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Util;

final class RotatingFileWriter
{
    public function __construct(private readonly string $path, private readonly int $maxBytes = 5242880, private readonly int $maxFiles = 3)
    {
    }

    public function write(string $line): void
    {
        $this->rotateIfNeeded();
        file_put_contents($this->path, $line, FILE_APPEND);
    }

    private function rotateIfNeeded(): void
    {
        if (file_exists($this->path) && filesize($this->path) >= $this->maxBytes) {
            for ($i = $this->maxFiles - 1; $i >= 1; --$i) {
                $old = $this->path.'.'.$i;
                $new = $this->path.'.'.($i + 1);
                if (file_exists($old)) {
                    if (file_exists($old) && !rename($old, $new)) {
                        throw new \RuntimeException(sprintf('Unable to rotate log segment from %s to %s.', $old, $new));
                    }
                }
            }
            if (!rename($this->path, $this->path.'.1')) {
                throw new \RuntimeException(sprintf('Unable to rotate active log file %s.', $this->path));
            }
        }
    }
}
