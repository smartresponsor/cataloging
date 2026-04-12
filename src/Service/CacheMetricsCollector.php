<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the cache metrics collector application service.
 */
final class CacheMetricsCollector
{
    /** @var array<string,int> */
    private array $hits = [];
    /** @var array<string,int> */
    private array $misses = [];

    /**
     * Handles the hit workflow.
     */
    public function hit(string $locale): void
    {
        $this->hits[$locale] = ($this->hits[$locale] ?? 0) + 1;
    }

    /**
     * Handles the miss workflow.
     */
    public function miss(string $locale): void
    {
        $this->misses[$locale] = ($this->misses[$locale] ?? 0) + 1;
    }

    /**
     * Handles the dump workflow.
     */
    public function dump(string $file): void
    {
        try {
            $directory = dirname($file);
            if ('.' !== $directory && !is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $encoded = json_encode([
                'hits' => $this->hits,
                'misses' => $this->misses,
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            file_put_contents($file, $encoded);
        } catch (\Throwable) {
            // Best-effort metrics only.
        }
    }
}
