<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides a process-local filesystem lock for tree mutations.
 */
final class TreeLock
{
    private string $directory;

    /** @var array<string, resource> */
    private array $handles = [];

    public function __construct(?string $directory = null)
    {
        $this->directory = $directory ?? rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'cataloging-tree-locks';
    }

    public function acquire(string $key): void
    {
        $normalizedKey = $this->normalizedKey($key);
        $this->ensureDirectoryExists();

        $path = $this->directory.DIRECTORY_SEPARATOR.$normalizedKey.'.lock';
        $handle = fopen($path, 'c+');
        if (false === $handle) {
            throw new \RuntimeException(sprintf('Unable to open lock file for key "%s".', $key));
        }

        if (!flock($handle, LOCK_EX)) {
            fclose($handle);
            throw new \RuntimeException(sprintf('Unable to acquire lock for key "%s".', $key));
        }

        $this->handles[$normalizedKey] = $handle;
    }

    public function release(string $key): void
    {
        $normalizedKey = $this->normalizedKey($key);
        if (!isset($this->handles[$normalizedKey])) {
            return;
        }

        $handle = $this->handles[$normalizedKey];
        flock($handle, LOCK_UN);
        fclose($handle);
        unset($this->handles[$normalizedKey]);
    }

    private function ensureDirectoryExists(): void
    {
        if (is_dir($this->directory)) {
            return;
        }

        if (!@mkdir($this->directory, 0777, true) && !is_dir($this->directory)) {
            throw new \RuntimeException(sprintf('Unable to create lock directory "%s".', $this->directory));
        }
    }

    private function normalizedKey(string $key): string
    {
        $normalized = trim($key);
        if ('' === $normalized) {
            throw new \InvalidArgumentException('Lock key must not be empty.');
        }

        $safe = preg_replace('/[^A-Za-z0-9_.-]+/', '_', $normalized);
        if (!is_string($safe)) {
            throw new \InvalidArgumentException('Lock key is not usable.');
        }

        return $safe;
    }
}
