<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Util;

/**
 * Provides the rotating file writer implementation.
 */
/** @noinspection PhpCSFixerValidationInspection */
final readonly class RotatingFileWriter
{
    /**
     * Initializes the rotating file writer service collaborators.
     */
    public function __construct(
        private string $path,
        private int $maxBytes = 5242880,
        private int $maxFiles = 3,
    ) {
    }

    /**
     * Handles the write workflow.
     *
     * @throws \RuntimeException
     */
    public function write(string $line): void
    {
        $this->rotateIfNeeded();
        file_put_contents($this->path, $line, FILE_APPEND);
    }

    /**
     * @throws \RuntimeException
     */
    private function rotateIfNeeded(): void
    {
        if (file_exists($this->path) && filesize($this->path) >= $this->maxBytes) {
            for ($fileIndex = $this->maxFiles - 1; $fileIndex >= 1; --$fileIndex) {
                $old = $this->path.'.'.$fileIndex;
                $new = $this->path.'.'.($fileIndex + 1);
                if (file_exists($old) && !rename($old, $new)) {
                    throw new \RuntimeException(sprintf('Unable to rotate log segment from %s to %s.', $old, $new));
                }
            }
            if (!rename($this->path, $this->path.'.1')) {
                throw new \RuntimeException(sprintf('Unable to rotate active log file %s.', $this->path));
            }
        }
    }
}
